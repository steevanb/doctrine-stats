<?php

declare(strict_types=1);

if (array_key_exists(1, $argv) === false) {
    throw new \Exception('You should add new version as first argument.');
}
$newVersion = $argv[1];

$changelogFilename = __DIR__ . '/../../changelog.md';
$lines = file($changelogFilename);
if (is_array($lines) === false) {
    throw new \Exception('Unable to read changelog.md.');
}

$masterTitle = '### master';
if (trim($lines[0]) !== $masterTitle) {
    throw new \Exception('changelog.md format is invalid: first line should be "' . $masterTitle . '".');
}

$previousVersion = null;
$lineIndex = 1;
while (is_string($previousVersion) === false) {
    if (substr($lines[$lineIndex], 0, 5) === '### [') {
        $posVersionEnd = strpos($lines[$lineIndex], ']');
        if (is_int($posVersionEnd) === false) {
            throw new \Exception(
                'changelog.md format is invalid: unable to find version end in "' . trim($lines[$lineIndex]) . '".'
            );
        }

        $previousVersion = substr($lines[$lineIndex], 5, $posVersionEnd - 5);
        if (is_numeric(str_replace('.', '', $previousVersion)) === false) {
            throw new \Exception(
                'changelog.md format is invalid: invalid previous version format "' . $previousVersion . '".'
            );
        }

        break;
    }

    $lineIndex++;
    if ($lineIndex > count($lines)) {
        throw new \Exception('changelog.md format is invalid: unable to found previous version.');
    }
}

$lines[-2] = $masterTitle . "\n";
$lines[-1] = "\n";
$lines[0] =
    '### [' . $newVersion . '](../../compare/' . $previousVersion . '...' . $newVersion . ') - '
    . (new DateTime())->format('Y-m-d')
    . "\n";

ksort($lines);
file_put_contents($changelogFilename, implode('', $lines));
