<?php

namespace steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DataCollector;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use steevanb\DoctrineStats\Bridge\DoctrineCollectorInterface;
use steevanb\DoctrineStats\Doctrine\DBAL\Logger\SqlLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector implements DoctrineCollectorInterface
{
    /** @var array */
    protected $lazyLoadedEntities = [];

    /** @var SqlLogger */
    protected $sqlLogger;

    /** @var array */
    protected $managedEntities = [];

    /** @var array */
    protected $hydrationTimes = [];

    /** @var int */
    protected $queriesAlert = 1;

    /** @var int */
    protected $managedEntitiesAlert = 10;

    /** @var int */
    protected $lazyLoadedEntitiesAlert = 1;

    /** @var int */
    protected $hydrationTimeAlert = 5;

    /** @var array */
    protected $hydratedEntities = [];

    /** @var ManagerRegistry */
    protected $doctrine;

    /**
     * @param SqlLogger $sqlLogger
     */
    public function __construct(SqlLogger $sqlLogger, ManagerRegistry $doctrine)
    {
        $this->sqlLogger = $sqlLogger;
        $this->doctrine = $doctrine;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine_stats';
    }

    public function reset()
    {
        $this->data = [];
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setQueriesAlert($count)
    {
        $this->queriesAlert = $count;

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setManagedEntitiesAlert($count)
    {
        $this->managedEntitiesAlert = $count;

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function setLazyLoadedEntitiesAlert($count)
    {
        $this->lazyLoadedEntitiesAlert = $count;

        return $this;
    }

    /**
     * @param int $time Time in milliseconds
     * @return $this
     */
    public function setHydrationTimeAlert($time)
    {
        $this->hydrationTimeAlert = $time;

        return $this;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param object $entity
     * @return $this
     */
    public function addLazyLoadedEntity(EntityManagerInterface $entityManager, $entity)
    {
        $className = get_class($entity);
        $classMetaData = $entityManager->getClassMetadata($className);
        $associations = array();
        foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $metaData) {
            foreach ($metaData->associationMappings as $field => $mapping) {
                if ($mapping['targetEntity'] === $classMetaData->name) {
                    $associations[] = array_merge(
                        $this->explodeClassParts($metaData->name),
                        ['field' => $field]
                    );
                }
            }
        }

        $this->lazyLoadedEntities[] = array_merge(
            $this->explodeClassParts($classMetaData->name),
            [
                'identifiers' => $classMetaData->getIdentifierValues($entity),
                'associations' => $associations,
                'queryIndex' => $this->sqlLogger->getCurrentQueryIndex()
            ]
        );

        return $this;
    }

    /**
     * @param string $hydratorClassName
     * @param float $time Time, in milliseconds
     * @return $this
     */
    public function addHydrationTime($hydratorClassName, $time)
    {
        if (isset($this->hydrationTimes[$hydratorClassName]) === false) {
            $this->hydrationTimes[$hydratorClassName] = [];
        }
        $this->hydrationTimes[$hydratorClassName][] = [
            'queryIndex' => $this->sqlLogger->getCurrentQueryIndex(),
            'time' => $time
        ];

        return $this;
    }

    /**
     * @param string $hydratorClassName
     * @param string $className
     * @param array $classIdentifiers
     * @return $this
     */
    public function addHydratedEntity($hydratorClassName, $className, $classIdentifiers)
    {
        if (array_key_exists($hydratorClassName, $this->hydratedEntities) === false) {
            $this->hydratedEntities[$hydratorClassName] = [];
        }
        if (array_key_exists($className, $this->hydratedEntities[$hydratorClassName]) === false) {
            $this->hydratedEntities[$hydratorClassName][$className] = [];
        }

        $this->hydratedEntities[$hydratorClassName][$className][] = $this->identifiersAsString($classIdentifiers);

        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->data = [
            'lazyLoadedEntities' => $this->lazyLoadedEntities,
            'queries' => $this->sqlLogger->getQueries(),
            'managedEntities' => $this->parseManagedEntities(),
            'hydrationTimes' => $this->hydrationTimes,
            'queriesAlert' => $this->queriesAlert,
            'managedEntitiesAlert' => $this->managedEntitiesAlert,
            'lazyLoadedEntitiesAlert' => $this->lazyLoadedEntitiesAlert,
            'hydrationTimeAlert' => $this->hydrationTimeAlert,
            'hydratedEntities' => $this->hydratedEntities
        ];
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        static $return = false;

        if ($return === false) {
            $return = [];
            foreach ($this->data['queries'] as $index => $query) {
                if (array_key_exists($query['sql'], $return) === false) {
                    $return[$query['sql']] = [
                        'queryTime' => 0,
                        'queryTimePercent' => 0,
                        'data' => [],
                        'lazyLoadedEntities' => [],
                        'hydrationTime' => 0,
                        'hydrationTimePercent' => 0,
                    ];
                }
                $return[$query['sql']]['queryTime'] += $query['time'] * 1000;
                $return[$query['sql']]['data'][] = ['params' => $query['params']];
                $return[$query['sql']]['backtraces'][$index] =
                    $query['backtrace'] === null
                        ? null
                        : (
                            class_exists('\DumpBacktrace')
                                ? \DumpBacktrace::getDump($query['backtrace'])
                                : \DebugBacktraceHtml::getDump($query['backtrace'])
                        );

                foreach ($this->data['lazyLoadedEntities'] as $lazyLoadedEntity) {
                    if ($lazyLoadedEntity['queryIndex'] === $index) {
                        if (array_key_exists($index, $return[$query['sql']]['lazyLoadedEntities']) === false) {
                            $return[$query['sql']]['lazyLoadedEntities'][$index] = [];
                        }
                        $return[$query['sql']]['lazyLoadedEntities'][$index][] = $lazyLoadedEntity;
                    }
                }

                foreach ($this->data['hydrationTimes'] as $hydrationTimes) {
                    foreach ($hydrationTimes as $hydrationTime) {
                        if ($hydrationTime['queryIndex'] === $index) {
                            $return[$query['sql']]['hydrationTime'] += $hydrationTime['time'];
                        }
                    }
                }
            }

            foreach ($return as &$queryData) {
                if ($queryData['hydrationTime'] === 0 && $queryData['queryTime'] === 0) {
                    $queryData['queryTimePercent'] = 100;
                } else {
                    $queryData['queryTimePercent'] = round(
                        ($queryData['queryTime'] * 100)
                        / ($queryData['hydrationTime'] + $queryData['queryTime'])
                    );
                }
                $queryData['hydrationTimePercent'] = 100 - $queryData['queryTimePercent'];
            }

            uasort($return, function ($queryA, $queryB) {
                return count($queryA['data']) < count($queryB['data']) ? 1 : -1;
            });
        }

        return $return;
    }

    /**
     * @return int
     */
    public function countQueries()
    {
        return count($this->data['queries']);
    }

    /**
     * @return float
     */
    public function getQueriesTime()
    {
        $return = 0;
        foreach ($this->getQueries() as $query) {
            $return += $query['queryTime'];
        }

        return round($return, 2);
    }

    /**
     * @return int
     */
    public function getQueriesTimePercent()
    {
        return $this->getDoctrineTime() > 0 ? round(($this->getQueriesTime() * 100) / $this->getDoctrineTime()) : 0;
    }

    /**
     * @return int
     */
    public function countDifferentQueries()
    {
        return count($this->getQueries());
    }

    /**
     * @return array
     */
    public function getLazyLoadedEntities()
    {
        return $this->data['lazyLoadedEntities'];
    }

    /**
     * @return int
     */
    public function countLazyLoadedEntities()
    {
        return count($this->data['lazyLoadedEntities']);
    }

    /**
     * @return int
     */
    public function countWarnings()
    {
        return $this->countLazyLoadedEntities();
    }

    /**
     * @param string $fullyQualifiedClassName
     * @return int
     */
    public function countLazyLoadedClass($fullyQualifiedClassName)
    {
        $count = 0;
        foreach ($this->getLazyLoadedEntities() as $lazyLoaded) {
            if ($lazyLoaded['namespace'] . '\\' . $lazyLoaded['className'] === $fullyQualifiedClassName) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array
     */
    public function getManagedEntities()
    {
        static $ordered = false;
        if ($ordered === false) {
            arsort($this->data['managedEntities']);
            $ordered = true;
        }

        return $this->data['managedEntities'];
    }

    /** @return int */
    public function countManagedEntities()
    {
        $return = 0;
        foreach ($this->getManagedEntities() as $stats) {
            $return += $stats['count'];
        }

        return $return;
    }

    /**
     * @return float
     */
    public function getHydrationTotalTime()
    {
        $return = 0;
        foreach ($this->data['hydrationTimes'] as $times) {
            foreach ($times as $time) {
                $return += $time['time'];
            }
        }

        return round($return, 2);
    }

    /**
     * @return array
     */
    public function getHydrationTimesByHydrator()
    {
        $return = [];
        foreach ($this->data['hydrationTimes'] as $hydratorClassName => $times) {
            $return[$hydratorClassName] = 0;
            foreach ($times as $time) {
                $return[$hydratorClassName] += $time['time'];
            }
        }

        return $return;
    }

    /**
     * @return int
     */
    public function getHydrationTimePercent()
    {
        return $this->getDoctrineTime() > 0
            ? round(($this->getHydrationTotalTime() * 100) / $this->getDoctrineTime())
            : 0;
    }

    /**
     * @return float
     */
    public function getDoctrineTime()
    {
        return round($this->getQueriesTime() + $this->getHydrationTotalTime(), 2);
    }

    /**
     * @return int
     */
    public function getQueriesAlert()
    {
        return $this->data['queriesAlert'];
    }

    /**
     * @return int
     */
    public function getManagedEntitiesAlert()
    {
        return $this->data['managedEntitiesAlert'];
    }

    /**
     * @return int
     */
    public function getLazyLoadedEntitiesAlert()
    {
        return $this->data['lazyLoadedEntitiesAlert'];
    }

    /**
     * @return int
     */
    public function getHydrationTimealert()
    {
        return $this->data['hydrationTimeAlert'];
    }

    /**
     * @return string|null
     */
    public function getToolbarStatus()
    {
        $alert =
            $this->countQueries() >= $this->getQueriesAlert()
            || $this->countManagedEntities() >= $this->data['managedEntitiesAlert']
            || $this->countLazyLoadedEntities() >= $this->data['lazyLoadedEntitiesAlert']
            || $this->getHydrationTotalTime() >= $this->data['hydrationTimeAlert'];

        return $alert ? 'red' : null;
    }

    /**
     * @return bool
     */
    public function isHydratorsOverloaded()
    {
        return $this->countQueries() === 0 || ($this->countQueries() > 0 && count($this->data['hydrationTimes']) > 0);
    }

    /**
     * @param string $hydrator
     * @return array
     */
    public function getHydratedEntities($hydrator)
    {
        return array_key_exists($hydrator, $this->data['hydratedEntities'])
            ? $this->data['hydratedEntities'][$hydrator]
            : [];
    }

    /**
     * @param string $hydrator
     * @return int
     */
    public function countHydratedEntities($hydrator)
    {
        $count = 0;
        foreach ($this->getHydratedEntities($hydrator) as $classes) {
            foreach ($classes as $identifiers) {
                $count += count($identifiers);
            }
        }

        return $count;
    }

    /**
     * @param $fullyClassifiedClassName
     * @return array
     */
    public function explodeClassParts($fullyClassifiedClassName)
    {
        $posBackSlash = strrpos($fullyClassifiedClassName, '\\');

        return [
            'namespace' => substr($fullyClassifiedClassName, 0, $posBackSlash),
            'className' => substr($fullyClassifiedClassName, $posBackSlash + 1)
        ];
    }

    /**
     * @param array $identifiers
     * @return array
     */
    public function mergeIdentifiers(array $identifiers)
    {
        $return = [];
        foreach ($identifiers as $identifier) {
            if (array_key_exists($identifier, $return) === false) {
                $return[$identifier] = 0;
            }
            $return[$identifier]++;
        }

        return $return;
    }

    /**
     * @param array $identifiers
     * @return string
     */
    public function identifiersAsString(array $identifiers)
    {
        $return = [];
        foreach ($identifiers as $name => $value) {
            $return[] = $name . ': ' . $value;
        }

        return implode(', ', $return);
    }

    /** @return array */
    protected function parseManagedEntities()
    {
        $return = [];
        foreach ($this->doctrine->getManagers() as $manager) {
            if ($manager instanceof EntityManagerInterface === false) {
                continue;
            }

            foreach ($manager->getUnitOfWork()->getIdentityMap() as $class => $entities) {
                $return[$class] = [
                    'count' => count($entities),
                    'ids' => array_keys($entities)
                ];
                sort($return[$class]['ids']);
            }
        }

        return $return;
    }
}
