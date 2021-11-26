<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Steevanb\DoctrineStats\{
    Bridge\DoctrineStatsBundle\DataCollector\DoctrineCollectorInterface,
    Doctrine\ORM\Event\PostCreateEntityEventArgs,
    Doctrine\ORM\Event\PostHydrationEventArgs,
    Doctrine\ORM\Event\PostLazyLoadEventArgs,
    Doctrine\ORM\Event\PreHydrationEventArgs
};

class DoctrineEventSubscriber implements EventSubscriber
{
    /** @var string */
    protected $preHydrationEventId;

    /** @var float */
    protected $preHydrationTime;

    /** @var DoctrineCollectorInterface  */
    protected $collector;

    public function __construct(DoctrineCollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    public function getSubscribedEvents(): array
    {
        return [
            PostLazyLoadEventArgs::EVENT_NAME,
            PreHydrationEventArgs::EVENT_NAME,
            PostHydrationEventArgs::EVENT_NAME,
            PostCreateEntityEventArgs::EVENT_NAME
        ];
    }

    public function postLazyLoad(PostLazyLoadEventArgs $eventArgs): void
    {
        $this
            ->collector
            ->addLazyLoadedEntity($eventArgs->getEntityManager(), $eventArgs->getEntity());
    }

    public function preHydration(PreHydrationEventArgs $eventArgs): void
    {
        if ($this->preHydrationEventId === null) {
            $this->preHydrationEventId = $eventArgs->getEventId();
            $this->preHydrationTime = microtime(true);
        }
    }

    public function postHydration(PostHydrationEventArgs $eventArgs): void
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

    public function postCreateEntity(PostCreateEntityEventArgs $eventArgs): void
    {
        $this->collector->addHydratedEntity(
            $eventArgs->getHydratorClassName(),
            $eventArgs->getClassName(),
            $eventArgs->getClassIdentifiers()
        );
    }
}
