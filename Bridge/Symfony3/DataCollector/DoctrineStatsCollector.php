<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3\DataCollector;

use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadingEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector
{
    /** @var PostLazyLoadingEventArgs[] */
    protected $lazyLoadings = array();

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine_stats';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array('lazyLoadings' => count($this->lazyLoadings));
    }

    /**
     * @return int
     */
    public function countLazyLoadings()
    {
        return $this->data['lazyLoadings'];
    }

    /**
     * @param PostLazyLoadingEventArgs $eventArgs
     */
    public function postLazyLoading(PostLazyLoadingEventArgs $eventArgs)
    {
        $this->lazyLoadings[] = $eventArgs;
    }
}
