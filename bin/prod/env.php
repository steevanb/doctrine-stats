<?php

declare(strict_types=1);

use Steevanb\ParallelProcess\{
    Console\Application\ParallelProcessesApplication,
    Process\Process
};
use Symfony\Component\Console\Input\ArgvInput;

require $_SERVER['COMPOSER_HOME'] . '/vendor/autoload.php';
$rootDir = dirname(__DIR__, 2);

$application = new ParallelProcessesApplication();

$composerProcess = (new Process([$rootDir . '/bin/composer', 'update', '--no-dev', '--ansi']))
    ->setName('composer update');

$phpPpDir = $rootDir . '/vendor/php-pp';
if (is_dir($phpPpDir)) {
    $rmPhpPpProcess = (new Process(['rm', '-rf', $phpPpDir]))->setName('rm -rf vendor/php-pp');
    $application->addProcess($rmPhpPpProcess);
    $composerProcess->getStartCondition()->addProcessSuccessful($rmPhpPpProcess);
}

$application->addProcess($composerProcess);

$application->run(new ArgvInput($argv));
