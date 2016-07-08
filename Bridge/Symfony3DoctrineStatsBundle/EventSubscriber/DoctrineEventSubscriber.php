<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\DataCollector\DoctrineStatsCollector;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadingEventArgs;

class DoctrineEventSubscriber implements EventSubscriber
{
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
        return array('postLazyLoading');
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
}
