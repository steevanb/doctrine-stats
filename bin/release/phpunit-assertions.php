<?php

declare(strict_types=1);

$xmlPath = __DIR__ . '/../../var/release/phpunit/junit.xml';
$xml = simplexml_load_file($xmlPath);
if ($xml instanceof SimpleXMLElement === false) {
    throw new \Exception('Unable to load ' . $xmlPath . '.');
}

$assertions = 0;
foreach ($xml->testsuite as $testsuite) {
    $testsuiteAssertions = $testsuite->attributes()['assertions'];
    if (is_numeric((string) $testsuiteAssertions) === false) {
        throw new \Exception('Invalid numeric format for attribute "assertions": "' . $testsuiteAssertions . '".');
    }

    $assertions += (int) $testsuiteAssertions;
}

echo number_format($assertions);
