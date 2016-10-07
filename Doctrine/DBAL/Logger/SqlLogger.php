<?php

namespace steevanb\DoctrineStats\Doctrine\DBAL\Logger;

use Doctrine\DBAL\Logging\SQLLogger as SQLLoggerInterface;

class SqlLogger implements SQLLoggerInterface
{
    /** @var array */
    protected $queries = [];

    /** @var int */
    protected $currentQueryIndex = -1;

    /** @var float|null */
    protected $start = null;

    /** @var bool */
    protected $backtraceEnabled = false;

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setBacktraceEnabled($enabled)
    {
        $this->backtraceEnabled = $enabled;

        return $this;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param array|null $types
     * @throws \Exception
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($this->backtraceEnabled) {
            if (class_exists('\DumpBacktrace') === false) {
                throw new \Exception(
                    'You need to add steevanb/php-backtrace in your dependencies to activate query backtrace.'
                );
            }
            $backtrace = \DumpBacktrace::getBacktraces();
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

    public function stopQuery()
    {
        $this->queries[$this->currentQueryIndex]['time'] = microtime(true) - $this->start;
    }

    /**
     * @return int
     */
    public function getCurrentQueryIndex()
    {
        return $this->currentQueryIndex;
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
