<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\ORM\EntityManagerInterface;

trait OverloadedHydratorTrait
{
    use HydrationEventsTrait;

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->_em;
    }

    /**
     * @param object $stmt
     * @param object $resultSetMapping
     * @return array
     */
    public function hydrateAll($stmt, $resultSetMapping, array $hints = [])
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateAll($stmt, $resultSetMapping, $hints);
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }

    /** @return mixed */
    public function hydrateRow()
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateRow();
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }
}
