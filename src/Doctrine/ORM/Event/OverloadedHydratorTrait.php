<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\DBAL\{
    Driver\ResultStatement,
    Result
};
use Doctrine\ORM\EntityManagerInterface;

trait OverloadedHydratorTrait
{
    use HydrationEventsTrait;

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->_em;
    }

    /**
     * @param Result|ResultStatement $stmt
     * @param object $resultSetMapping
     * @param array<string, string> $hints
     * @return array<string|int, object>
     */
    public function hydrateAll($stmt, $resultSetMapping, array $hints = []): array
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateAll($stmt, $resultSetMapping, $hints);
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }

    /** @return array<mixed>|false */
    public function hydrateRow()
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateRow();
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }
}
