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

    /**
     * @param string $sql
     * @param array|null $params
     * @param array|null $types
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);
        $this->queries[++$this->currentQueryIndex] = [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
            'time' => 0
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
