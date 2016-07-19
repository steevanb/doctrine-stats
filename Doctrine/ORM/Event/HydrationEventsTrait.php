<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

trait HydrationEventsTrait
{
    /**
     * @return string
     */
    protected function dispatchPreHydrationEvent()
    {
        $eventArgs = new PreHydrationEventArgs(get_class($this));
        $this->_em->getEventManager()->dispatchEvent(PreHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $eventArgs->getEventId();
    }

    /**
     * @param string $preHydrationEventId
     * @return $this
     */
    protected function dispatchPostHydrationEvent($preHydrationEventId)
    {
        $eventArgs = new PostHydrationEventArgs($preHydrationEventId, get_class($this));
        $this->_em->getEventManager()->dispatchEvent(PostHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $this;
    }
}
