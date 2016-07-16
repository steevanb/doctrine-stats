<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\ORM\EntityManagerInterface;

trait HydrationEventsTrait
{
    /**
     * @return EntityManagerInterface
     */
    abstract protected function getEntityManager();

    /**
     * @return string
     */
    protected function dispatchPreHydrationEvent()
    {
        $eventArgs = new PreHydrationEventArgs(get_class($this));
        $this->getEntityManager()->getEventManager()->dispatchEvent(PreHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $eventArgs->getEventId();
    }

    /**
     * @param string $preHydrationEventId
     * @return $this
     */
    protected function dispatchPostHydrationEvent($preHydrationEventId)
    {
        $eventArgs = new PostHydrationEventArgs($preHydrationEventId, get_class($this));
        $this->getEntityManager()->getEventManager()->dispatchEvent(PostHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $this;
    }
}
