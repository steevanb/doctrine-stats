<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\ORM\{
    EntityManagerInterface,
    Mapping\ClassMetadata
};

trait HydrationEventsTrait
{
    abstract protected function getEntityManager(): EntityManagerInterface;

    protected function dispatchPreHydrationEvent(): string
    {
        $eventArgs = new PreHydrationEventArgs(get_class($this));
        $this->getEntityManager()->getEventManager()->dispatchEvent(PreHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $eventArgs->getEventId();
    }

    protected function dispatchPostHydrationEvent(string $preHydrationEventId): self
    {
        $eventArgs = new PostHydrationEventArgs($preHydrationEventId, get_class($this));
        $this->getEntityManager()->getEventManager()->dispatchEvent(PostHydrationEventArgs::EVENT_NAME, $eventArgs);

        return $this;
    }

    /** @param array<mixed> $data */
    protected function dispatchPostCreateEntityEvent(ClassMetadata $classMetaData, array $data): self
    {
        $identifiers = [];
        foreach ($classMetaData->getIdentifierFieldNames() as $identifier) {
            if (array_key_exists($identifier, $data)) {
                $identifiers[$identifier] = $data[$identifier];
            }
        }

        $eventArgs = new PostCreateEntityEventArgs(get_class($this), $classMetaData->name, $identifiers);
        $this->getEntityManager()->getEventManager()->dispatchEvent(PostCreateEntityEventArgs::EVENT_NAME, $eventArgs);

        return $this;
    }
}
