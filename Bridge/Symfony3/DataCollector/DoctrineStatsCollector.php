<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3\DataCollector;

use Doctrine\ORM\EntityManagerInterface;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadingEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector
{
    /** @var PostLazyLoadingEventArgs[] */
    protected $lazyLoadings = array();

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

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
        $this->data = array('lazyLoadings' => $this->lazyLoadings);
    }

    /**
     * @return array
     */
    public function getLazyLoadings()
    {
        return $this->data['lazyLoadings'];
    }

    /**
     * @return int
     */
    public function countLazyLoadings()
    {
        return count($this->data['lazyLoadings']);
    }

    /**
     * @return int
     */
    public function countWarnings()
    {
        return $this->countLazyLoadings();
    }

    /**
     * @param PostLazyLoadingEventArgs $eventArgs
     */
    public function postLazyLoading(PostLazyLoadingEventArgs $eventArgs)
    {
        $this->lazyLoadings[] = $eventArgs;
    }
}
