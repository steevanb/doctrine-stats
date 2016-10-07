<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;

class PostLazyLoadEventArgs extends EventArgs
{
    const EVENT_NAME = 'postLazyLoad';

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var object */
    protected $entity;

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entity
     */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
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
    public function getEntity()
    {
        return $this->entity;
    }
}
