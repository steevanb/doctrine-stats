<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;

class PostLazyLoadingEventArgs extends EventArgs
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Proxy */
    protected $proxy;

    /**
     * @param Proxy $proxy
     */
    public function __construct(EntityManagerInterface $entityManager, Proxy $proxy)
    {
        $this->entityManager = $entityManager;
        $this->proxy = $proxy;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }
}
