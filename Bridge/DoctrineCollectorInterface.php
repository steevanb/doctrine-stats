<?php

namespace steevanb\DoctrineStats\Bridge;

use Doctrine\ORM\EntityManagerInterface;

interface DoctrineCollectorInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entity
     * @return $this
     */
    public function addLazyLoadedEntity(EntityManagerInterface $entityManager, $entity);

    /**
     * @param string $hydratorClassName
     * @param float $time Time, in milliseconds
     * @return $this
     */
    public function addHydrationTime($hydratorClassName, $time);

    /**
     * @param string $className
     * @param string $identifiers
     * @return $this
     */
    public function addManagedEntity($className, $identifiers);
}
