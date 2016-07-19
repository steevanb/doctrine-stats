<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

trait OverloadedHydratorTrait
{
    use HydrationEventsTrait;

    /**
     * @param object $stmt
     * @param object $resultSetMapping
     * @param array $hints
     * @return array
     */
    public function hydrateAll($stmt, $resultSetMapping, array $hints = array())
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
