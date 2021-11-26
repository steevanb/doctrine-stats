<?php

declare(strict_types=1);

$xmlPath = __DIR__ . '/../../var/release/phpunit/coverage/xml/index.xml';
$xml = simplexml_load_file($xmlPath);
if ($xml instanceof SimpleXMLElement === false) {
    throw new \Exception('Unable to load ' . $xmlPath . '.');
}

foreach ($xml->project->directory as $directory) {
    if ((string) $directory->attributes()['name'] === '/') {
        echo floor((float) $directory->totals->lines->attributes()['percent']);
        exit;
    }
}

throw new \Exception('Coverage not found.');
