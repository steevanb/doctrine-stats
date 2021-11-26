<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\{
    EntityManagerInterface,
    Proxy\Proxy
};

class PostLazyLoadEventArgs extends EventArgs
{
    public const EVENT_NAME = 'postLazyLoad';

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var object */
    protected $entity;

    /** @param object $entity */
    public function __construct(EntityManagerInterface $entityManager, $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    public function getEntityManager(): EntityManagerInterface
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
