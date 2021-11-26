<?php

declare(strict_types=1);

$projectPath = __DIR__ . '/../..';

return [
    'composerJsonPath' => $projectPath . '/composer.json',
    'vendorPath' => $projectPath . '/vendor/',
    'scanDirectories' => [
        $projectPath . '/src/',
        $projectPath . '/tests/',
        $projectPath . '/config/'
    ],
    'requireDev' => false
];
