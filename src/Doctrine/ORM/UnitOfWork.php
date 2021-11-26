<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM;

use Doctrine\ORM\{
    EntityManagerInterface,
    PersistentCollection,
    UnitOfWork as DoctrineUnitOfWork
};
use Steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;

class UnitOfWork extends DoctrineUnitOfWork
{
    public function loadCollection(PersistentCollection $collection)
    {
        parent::loadCollection($collection);

        $em = $this->getParentEntityManager();
        foreach ($collection->toArray() as $element) {
            $postLazyloadEventArgs = new PostLazyLoadEventArgs($em, $element);
            $em->getEventManager()->dispatchEvent(PostLazyLoadEventArgs::EVENT_NAME, $postLazyloadEventArgs);
        }
    }

    protected function getParentEntityManager(): EntityManagerInterface
    {
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), 'em');
        $reflectionProperty->setAccessible(true);
        $entityManager = $reflectionProperty->getValue($this);
        $reflectionProperty->setAccessible(false);

        return $entityManager;
    }
}
