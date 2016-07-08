<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\DataCollector\DoctrineStatsCollector;

class ManagedEntitiesListener
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /** @var DoctrineStatsCollector */
    protected $collector;

    /**
     * @param ManagerRegistry $doctrine
     * @param DoctrineStatsCollector $collector
     */
    public function __construct(ManagerRegistry $doctrine, DoctrineStatsCollector $collector)
    {
        $this->doctrine = $doctrine;
        $this->collector = $collector;
    }

    public function saveManagedEntities()
    {
        /** @var EntityManagerInterface $manager */
        foreach ($this->doctrine->getManagers() as $manager) {
            foreach ($manager->getUnitOfWork()->getIdentityMap() as $className => $entities) {
                foreach (array_keys($entities) as $identifiers) {
                    $this->collector->addManagedEntity($className, $identifiers);
                }
            }
        }
    }
}
