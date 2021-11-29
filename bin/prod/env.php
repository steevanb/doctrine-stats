<?php

declare(strict_types=1);

use Steevanb\ParallelProcess\{
    Console\Application\ParallelProcessesApplication,
    Process\Process
};
use Symfony\Component\Console\Input\ArgvInput;

require $_SERVER['COMPOSER_HOME'] . '/vendor/autoload.php';
$rootDir = dirname(__DIR__, 2);

(new ParallelProcessesApplication())
    ->addProcess(
        (new Process([$rootDir . '/bin/composer', 'update', '--no-dev', '--ansi']))
            ->setName('composer update')
    )
    ->run(new ArgvInput($argv));
