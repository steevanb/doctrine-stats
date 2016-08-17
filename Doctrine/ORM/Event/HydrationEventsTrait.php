<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\ORM\Mapping\ClassMetadata;

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

    /**
     * @param ClassMetadata $classMetaData
     * @param array $data
     * @return $this
     */
    protected function dispatchPostCreateEntityEvent(ClassMetadata $classMetaData, array $data)
    {
        $identifiers = [];
        foreach ($classMetaData->getIdentifierFieldNames() as $identifier) {
            if (array_key_exists($identifier, $data)) {
                $identifiers[$identifier] = $data[$identifier];
            }
        }

        $eventArgs = new PostCreateEntityEventArgs(get_class($this), $classMetaData->name, $identifiers);
        $this->_em->getEventManager()->dispatchEvent(PostCreateEntityEventArgs::EVENT_NAME, $eventArgs);

        return $this;
    }
}
