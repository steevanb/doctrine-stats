<?php

declare(strict_types=1);

$xmlPath = __DIR__ . '/../../var/release/phpunit/junit.xml';
$xml = simplexml_load_file($xmlPath);
if ($xml instanceof SimpleXMLElement === false) {
    throw new \Exception('Unable to load ' . $xmlPath . '.');
}

$tests = 0;
foreach ($xml->testsuite as $testsuite) {
    $testsuiteTests = $testsuite->attributes()['tests'];
    if (is_numeric((string) $testsuiteTests) === false) {
        throw new \Exception('Invalid numeric format for attribute "test": "' . $testsuiteTests . '".');
    }

    $tests += (int) $testsuiteTests;
}

echo number_format($tests);
