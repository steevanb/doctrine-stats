<?php

namespace steevanb\DoctrineStats\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork as DoctrineUnitOfWork;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PreLazyLoadEventArgs;

class UnitOfWork extends DoctrineUnitOfWork
{
    /**
     * Initializes (loads) an uninitialized persistent collection of an entity.
     *
     * @param \Doctrine\ORM\PersistentCollection $collection The collection to initialize.
     *
     * @return void
     *
     * @todo Maybe later move to EntityManager#initialize($proxyOrCollection). See DDC-733.
     */
    public function loadCollection(PersistentCollection $collection)
    {
        $em = $this->getParentEntityManager();

        $preLazyloadEventArgs = new PreLazyLoadEventArgs();
        $em->getEventManager()->dispatchEvent(PreLazyLoadEventArgs::EVENT_NAME, $preLazyloadEventArgs);

        parent::loadCollection($collection);

        foreach ($collection->toArray() as $element) {
            $postLazyloadEventArgs = new PostLazyLoadEventArgs($em, $element, $preLazyloadEventArgs->getEventId());
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
