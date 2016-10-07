<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\ORM\EntityManagerInterface;

trait OverloadedHydratorTrait
{
    use HydrationEventsTrait;

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->_em;
    }

    /**
     * @param object $stmt
     * @param object $resultSetMapping
     * @param array $hints
     * @return array
     */
    public function hydrateAll($stmt, $resultSetMapping, array $hints = [])
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateAll($stmt, $resultSetMapping, $hints);
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }

    /**
     * @return mixed
     */
    public function hydrateRow()
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateRow();
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }
}
