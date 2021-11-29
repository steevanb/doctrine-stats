<?php

declare(strict_types=1);

use Steevanb\ParallelProcess\{
    Console\Application\ParallelProcessesApplication,
    Process\Process,
    Process\ProcessArray
};
use PhpPp\Core\Component\Collection\StringCollection;
use Symfony\Component\Console\Input\ArgvInput;

require $_SERVER['COMPOSER_HOME'] . '/vendor/autoload.php';
require dirname(__DIR__, 2) . '/vendor/autoload.php';

function createPhpstanProcesses(string $phpVersion = null): ProcessArray
{
    $phpVersions = is_string($phpVersion) ? [$phpVersion] : ['7.4', '8.0'];

    $return = new ProcessArray();
    foreach ($phpVersions as $loopPhpVersion) {
        $return->offsetSet(null, createPhpstanProcess($loopPhpVersion));
    }

    return $return;
}

function createPhpstanProcess(string $phpVersion): Process
{
    return (new Process([__DIR__ . '/phpstan', '--php=' . $phpVersion]))
        ->setName('phpstan --php=' . $phpVersion);
}

$phpVersion = null;
$applicationArgv = [];
foreach ($argv as $arg) {
    if (substr($arg, 0, 6) === '--php=') {
        $phpVersion = substr($arg, 6);
    } else {
        $applicationArgv[] = $arg;
    }
}

(new ParallelProcessesApplication())
    ->addProcesses(createPhpstanProcesses($phpVersion))
    ->run(new ArgvInput($applicationArgv));
