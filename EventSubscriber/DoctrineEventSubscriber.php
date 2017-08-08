<?php

namespace steevanb\DoctrineStats\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use steevanb\DoctrineStats\Bridge\DoctrineCollectorInterface;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostCreateEntityEventArgs;
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

    /** @param DoctrineCollectorInterface $collector */
    public function __construct(DoctrineCollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /** @return array */
    public function getSubscribedEvents()
    {
        return [
            PostLazyLoadEventArgs::EVENT_NAME,
            PreHydrationEventArgs::EVENT_NAME,
            PostHydrationEventArgs::EVENT_NAME,
            PostCreateEntityEventArgs::EVENT_NAME
        ];
    }

    /** @param PostLazyLoadEventArgs $eventArgs */
    public function postLazyLoad(PostLazyLoadEventArgs $eventArgs)
    {
        $this
            ->collector
            ->addLazyLoadedEntity($eventArgs->getEntityManager(), $eventArgs->getEntity());
    }

    /** @param PreHydrationEventArgs $eventArgs */
    public function preHydration(PreHydrationEventArgs $eventArgs)
    {
        if ($this->preHydrationEventId === null) {
            $this->preHydrationEventId = $eventArgs->getEventId();
            $this->preHydrationTime = microtime(true);
        }
    }

    /** @param PostHydrationEventArgs $eventArgs */
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

    /** @param PostCreateEntityEventArgs $eventArgs */
    public function postCreateEntity(PostCreateEntityEventArgs $eventArgs)
    {
        $this->collector->addHydratedEntity(
            $eventArgs->getHydratorClassName(),
            $eventArgs->getClassName(),
            $eventArgs->getClassIdentifiers()
        );
    }
}
