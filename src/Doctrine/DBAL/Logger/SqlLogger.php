<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\DBAL\Logger;

use Doctrine\DBAL\Logging\SQLLogger as SQLLoggerInterface;

class SqlLogger implements SQLLoggerInterface
{
    /** @var array<array<mixed>> */
    protected $queries = [];

    /** @var int */
    protected $currentQueryIndex = -1;

    /** @var float|null */
    protected $start = null;

    /** @var bool */
    protected $backtraceEnabled = false;

    public function setBacktraceEnabled(bool $enabled): self
    {
        $this->backtraceEnabled = $enabled;

        return $this;
    }

    /** @param string $sql */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($this->backtraceEnabled) {
            $isDumpBacktrace = class_exists('\DumpBacktrace');
            $isDebugBacktrace = class_exists('\DebugBacktrace');
            if ($isDumpBacktrace === false && $isDebugBacktrace === false) {
                throw new \Exception(
                    'You need require steevanb/php-backtrace ^1.1||^2.0 to activate query backtrace. '
                    . 'Example with composer : composer require --dev steevanb/php-backtrace ^1.1||^2.0'
                );
            }
            $backtrace = $isDumpBacktrace ? \DumpBacktrace::getBacktraces() : \DebugBacktrace::getBacktraces();
        } else {
            $backtrace = null;
        }

        $this->start = microtime(true);
        $this->queries[++$this->currentQueryIndex] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'time' => 0,
            'backtrace' => $backtrace
        ];
    }

    public function stopQuery(): void
    {
        $this->queries[$this->currentQueryIndex]['time'] = microtime(true) - ($this->start ?? 0);
    }

    public function getCurrentQueryIndex(): int
    {
        return $this->currentQueryIndex;
    }

    /** @return array<array<mixed>> */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
