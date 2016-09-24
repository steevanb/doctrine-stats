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

    /** @var string */
    protected $preLazyLoadEventId;

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entity
     * @param string $preLazyLoadEventId
     */
    public function __construct(EntityManagerInterface $entityManager, $entity, $preLazyLoadEventId)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
        $this->preLazyLoadEventId = $preLazyLoadEventId;
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

    /**
     * @return string
     */
    public function getPreLazyLoadEventId()
    {
        return $this->preLazyLoadEventId;
    }
}
