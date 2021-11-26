<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DataCollector;

use Doctrine\ORM\EntityManagerInterface;

interface DoctrineCollectorInterface
{
    /** @param object $entity */
    public function addLazyLoadedEntity(EntityManagerInterface $entityManager, $entity): self;

    /** @param float $time Time in milliseconds */
    public function addHydrationTime(string $hydratorClassName, float $time): self;

    public function addHydratedEntity(string $hydratorClassName, string $className, array $classIdentifiers): self;
}
