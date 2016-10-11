<?php

namespace steevanb\DoctrineStats\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork as DoctrineUnitOfWork;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;

class UnitOfWork extends DoctrineUnitOfWork
{
    /**
     * @param PersistentCollection $collection
     */
    public function loadCollection(PersistentCollection $collection)
    {
        parent::loadCollection($collection);

        $em = $this->getParentEntityManager();
        foreach ($collection->toArray() as $element) {
            $postLazyloadEventArgs = new PostLazyLoadEventArgs($em, $element);
            $em->getEventManager()->dispatchEvent(PostLazyLoadEventArgs::EVENT_NAME, $postLazyloadEventArgs);
        }
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getParentEntityManager()
    {
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), 'em');
        $reflectionProperty->setAccessible(true);
        $entityManager = $reflectionProperty->getValue($this);
        $reflectionProperty->setAccessible(false);

        return $entityManager;
    }
}
