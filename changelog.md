1.1.0 (2016-08-17)
------------------

- Add $identifiers type in DoctrineCollectorInterface::addManagedentity($className, array $identifiers)
- Add DoctrineCollectorInterface::addHydratedEntity($hydratorClassName, $className, $classIdentifiers)
- Add DoctrineStatsCollector::addHydratedEntity($hydratorClassName, $className, $classIdentifiers)
- All identifiers now shown with same graphism in Symfony profiler
- All class names now shown with same graphism in Symfony profiler
- Remove useless monolog.logger tag to doctrine_stats.event_subscriber.doctrine Symfony service
- Add postCreateEntity event
- Add hydrated entities to Symfony profiler, for all hydrators who dispatch postCreateEntity event
(can't do that for Doctrine hydrators at the moment)
- Add HydrationEventsTrait::dispatchPostCreateEntityEvent(ClassMetadata $classMetaData, array $data)
- Add DoctrineEventSubscriber::postCreateEntity(PostCreateEntityEventArgs $eventArgs)

1.0.3 (2016-08-10)
------------------

- Fix division by zero in DoctrineStatsCollector::getHydrationTimePercent() and getQueriesTimePercent()

1.0.2 (2016-08-08)
------------------

- Add queries time in Symfony WebProfiler
- Add queries time percent and hydration time percent in Symfony WebProfiler

1.0.1 (2016-08-05)
------------------

- Fix DoctrineEventSubscriber::postLoad() call to addManagedEntity(), only managed entities will trigger this call

1.0.0 (2016-07-21)
------------------

- Add steevanb\DoctrineStats\Doctrine\ORM\EntityManager to overload Doctrine\ORM\Proxy\ProxyFactory
- Add steevanb\DoctrineStats\Doctrine\ORM\Proxy\ProxyFactory to add postLazyLoad event
- Add steevanb\DoctrineStats\EventSubscriber\DoctrineEventSubscriber to collect doctrine statistics
- Add ArrayHydrator, ObjectHydrator, ScalarHydrator, SimpleObjectHydrator and SingleScalarHydrator
to ComposerOverloadClass, to add preHydration and postHydration events
- Add Symfony2 and Symfony3 bridge with DoctrineStatsBundle

