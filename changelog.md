### [1.4.0](../../compare/1.3.3...1.4.0) - 2020-12-07

- Replace `Symfony\Bridge\Doctrine\RegistryInterface` (removed) by `Doctrine\Common\Persistence\ManagerRegistry` in `DoctrineStatsCollector`

### [1.3.3](../../compare/1.3.2...1.3.3) - 2017-12-07

- [[Gemorroj](https://github.com/Gemorroj)] Fix template path for Symfony 4
- Add _DoctrineStatsCollector::reset()_ for Symfony 4

### [1.3.2](../../compare/1.3.1...1.3.2) - 2017-11-17

- [[gsdevme](https://github.com/gsdevme)] Use _ManagerRegistry::getManagers()_ instead of _getEntityManagers()_

### [1.3.1](../../compare/1.3.0...1.3.1) - 2017-08-09

- Fix ternary operator syntax in _DoctrineStatsCollector_
- Fix plural for _Show identifiers_ and _Hide identifiers_ in Symfony WebProfiler panel

### [1.3.0](../../compare/1.2.1...1.3.0) - 2017-08-09

- [BC] Remove _DoctrineCollectorInterface::addManagedEntity()_ : Doctrine do not always trigger _postLoad_ event, so we can't use it to retrieve informations
- [BC] Remove _DoctrineStatsCollector::addManagedEntity()_
- _steevanb/php-backtrace_ dependency could be _^1.1_ or _^2.0_ now (_^1.1_ before)
- Group backtraces in _Show backtraces_, instead of one _Show backtrace #X_ per query

### [1.2.1](../../compare/1.2.0...1.2.1) - 2017-04-13

- #4 Fix division by zero if query and hydration time equal 0

### [1.2.0](../../compare/1.1.0...1.2.0) - 2016-11-10

- Disable panels when hydrators are not overloaded
- Add nice debug_backtrace() for each query
- Add SQL time, hydration time and Doctrine time for each query
- Add type (Manual / Lazy loading) for each query
- Add Show entity for each query

### [1.1.0](../../compare/1.0.3...1.1.0) - 2016-08-17

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

### [1.0.3](../../compare/1.0.2...1.0.3) - 2016-08-10

- Fix division by zero in DoctrineStatsCollector::getHydrationTimePercent() and getQueriesTimePercent()

### [1.0.2](../../compare/1.0.1...1.0.2) - 2016-08-08

- Add queries time in Symfony WebProfiler
- Add queries time percent and hydration time percent in Symfony WebProfiler

### [1.0.1](../../compare/1.0.0...1.0.1) - 2016-08-05

- Fix DoctrineEventSubscriber::postLoad() call to addManagedEntity(), only managed entities will trigger this call

### 1.0.0 - 2016-07-21

- Add steevanb\DoctrineStats\Doctrine\ORM\EntityManager to overload Doctrine\ORM\Proxy\ProxyFactory
- Add steevanb\DoctrineStats\Doctrine\ORM\Proxy\ProxyFactory to add postLazyLoad event
- Add steevanb\DoctrineStats\EventSubscriber\DoctrineEventSubscriber to collect doctrine statistics
- Add ArrayHydrator, ObjectHydrator, ScalarHydrator, SimpleObjectHydrator and SingleScalarHydrator
to ComposerOverloadClass, to add preHydration and postHydration events
- Add Symfony2 and Symfony3 bridge with DoctrineStatsBundle

