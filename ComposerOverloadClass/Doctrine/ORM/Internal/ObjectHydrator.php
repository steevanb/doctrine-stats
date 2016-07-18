<?php

namespace Doctrine\ORM\Internal\Hydration;

use Doctrine\ORM\EntityManagerInterface;
use steevanb\DoctrineStats\Doctrine\ORM\Event\HydrationEventsTrait;

class ObjectHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\ObjectHydrator
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
     * @return array
     */
    protected function hydrateAllData()
    {
        $eventId = $this->dispatchPreHydrationEvent();
        $return = parent::hydrateAllData();
        $this->dispatchPostHydrationEvent($eventId);

        return $return;
    }
}
