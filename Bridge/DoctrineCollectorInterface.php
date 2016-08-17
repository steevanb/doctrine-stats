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
     * @param array $identifiers
     * @return $this
     */
    public function addManagedEntity($className, array $identifiers);

    /**
     * @param string $hydratorClassName
     * @param string $className
     * @param array $classIdentifiers
     * @return $this
     */
    public function addHydratedEntity($hydratorClassName, $className, $classIdentifiers);
}
