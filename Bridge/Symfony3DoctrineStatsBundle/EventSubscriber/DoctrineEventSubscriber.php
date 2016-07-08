<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Proxy\Proxy;
use steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\DataCollector\DoctrineStatsCollector;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadingEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{
    /** @var array */
    protected $loaded = array();

    /** @var DoctrineStatsCollector */
    protected $doctrineStatsCollector;

    /**
     * @param DoctrineStatsCollector $doctrineStatsCollector
     */
    public function __construct(DoctrineStatsCollector $doctrineStatsCollector)
    {
        $this->doctrineStatsCollector = $doctrineStatsCollector;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            PostLazyLoadingEventArgs::EVENT_NAME,
            Events::postLoad
        );
    }

    /**
     * @param PostLazyLoadingEventArgs $eventArgs
     */
    public function postLazyLoading(PostLazyLoadingEventArgs $eventArgs)
    {
        $this
            ->doctrineStatsCollector
            ->addLazyLoadedEntity($eventArgs->getEntityManager(), $eventArgs->getProxy());
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

        $this->loaded[$className][$identifiersStr] = true;
    }
}
