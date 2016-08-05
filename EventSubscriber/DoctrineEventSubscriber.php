<?php

namespace steevanb\DoctrineStats\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Proxy\Proxy;
use steevanb\DoctrineStats\Bridge\DoctrineCollectorInterface;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostHydrationEventArgs;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PreHydrationEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{
    /** @var string */
    protected $preHydrationEventId;

    /** @var float */
    protected $preHydrationTime;

    /** @var DoctrineCollectorInterface  */
    protected $collector;

    /**
     * @param DoctrineCollectorInterface $collector
     */
    public function __construct(DoctrineCollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            PostLazyLoadEventArgs::EVENT_NAME,
            PreHydrationEventArgs::EVENT_NAME,
            PostHydrationEventArgs::EVENT_NAME,
            Events::postLoad
        );
    }

    /**
     * @param PostLazyLoadEventArgs $eventArgs
     */
    public function postLazyLoad(PostLazyLoadEventArgs $eventArgs)
    {
        $this
            ->collector
            ->addLazyLoadedEntity($eventArgs->getEntityManager(), $eventArgs->getProxy());
    }

    /**
     * @param PreHydrationEventArgs $eventArgs
     */
    public function preHydration(PreHydrationEventArgs $eventArgs)
    {
        if ($this->preHydrationEventId === null) {
            $this->preHydrationEventId = $eventArgs->getEventId();
            $this->preHydrationTime = microtime(true);
        }
    }

    /**
     * @param PostHydrationEventArgs $eventArgs
     */
    public function postHydration(PostHydrationEventArgs $eventArgs)
    {
        if ($this->preHydrationEventId === $eventArgs->getPreHydrationEventId()) {
            $postHydrationTime = microtime(true);
            $this->collector->addHydrationTime(
                $eventArgs->getHydratorClassName(),
                ($postHydrationTime - $this->preHydrationTime) * 1000
            );
            $this->preHydrationEventId = null;
            $this->preHydrationTime = null;
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $metaData = $eventArgs->getEntityManager()->getClassMetadata(get_class($eventArgs->getEntity()));
        if ($eventArgs->getEntity() instanceof Proxy) {
            $className = $metaData->name;
        } else {
            $className = get_class($eventArgs->getEntity());
        }
        $identifiers = $metaData->getIdentifierValues($eventArgs->getEntity());
        $identifiersStr = implode(', ', $identifiers);

        if ($eventArgs->getEntityManager()->getUnitOfWork()->isInIdentityMap($eventArgs->getEntity())) {
            $this->collector->addManagedEntity($className, $identifiersStr);
        }
    }
}
