<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Bridge\DoctrineStatsBundle\DataCollector;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Steevanb\DoctrineStats\Doctrine\DBAL\Logger\SqlLogger;
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector implements DoctrineCollectorInterface
{
    /** @var array */
    protected $lazyLoadedEntities = [];

    /** @var SqlLogger */
    protected $sqlLogger;

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

    public function __construct(SqlLogger $sqlLogger, ManagerRegistry $doctrine)
    {
        $this->sqlLogger = $sqlLogger;
        $this->doctrine = $doctrine;
    }

    /** @return string */
    public function getName()
    {
        return 'doctrine_stats';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function setQueriesAlert(int $count): self
    {
        $this->queriesAlert = $count;

        return $this;
    }

    public function setManagedEntitiesAlert(int $count): self
    {
        $this->managedEntitiesAlert = $count;

        return $this;
    }

    public function setLazyLoadedEntitiesAlert(int $count): self
    {
        $this->lazyLoadedEntitiesAlert = $count;

        return $this;
    }

    /** @param int $time Time in milliseconds */
    public function setHydrationTimeAlert(int $time): self
    {
        $this->hydrationTimeAlert = $time;

        return $this;
    }

    /** @param object $entity */
    public function addLazyLoadedEntity(EntityManagerInterface $entityManager, $entity): DoctrineCollectorInterface
    {
        $className = get_class($entity);
        $classMetaData = $entityManager->getClassMetadata($className);
        $associations = [];
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

    /** @param float $time Time, in milliseconds */
    public function addHydrationTime(string $hydratorClassName, float $time): DoctrineCollectorInterface
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

    public function addHydratedEntity(
        string $hydratorClassName,
        string $className,
        array $classIdentifiers
    ): DoctrineCollectorInterface {
        if (array_key_exists($hydratorClassName, $this->hydratedEntities) === false) {
            $this->hydratedEntities[$hydratorClassName] = [];
        }
        if (array_key_exists($className, $this->hydratedEntities[$hydratorClassName]) === false) {
            $this->hydratedEntities[$hydratorClassName][$className] = [];
        }

        $this->hydratedEntities[$hydratorClassName][$className][] = $this->identifiersAsString($classIdentifiers);

        return $this;
    }

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

    public function getQueries(): array
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

            uasort(
                $return,
                function (array $queryA, array $queryB) {
                    return count($queryA['data']) < count($queryB['data']) ? 1 : -1;
                }
            );
        }

        return $return;
    }

    public function countQueries(): int
    {
        return count($this->data['queries']);
    }

    public function getQueriesTime(): float
    {
        $return = 0;
        foreach ($this->getQueries() as $query) {
            $return += $query['queryTime'];
        }

        return round($return, 2);
    }

    public function getQueriesTimePercent(): float
    {
        return $this->getDoctrineTime() > 0
            ? round(($this->getQueriesTime() * 100) / $this->getDoctrineTime())
            : 0;
    }

    public function countDifferentQueries(): int
    {
        return count($this->getQueries());
    }

    public function getLazyLoadedEntities(): array
    {
        return $this->data['lazyLoadedEntities'];
    }

    public function countLazyLoadedEntities(): int
    {
        return count($this->data['lazyLoadedEntities']);
    }

    public function countWarnings(): int
    {
        return $this->countLazyLoadedEntities();
    }

    public function countLazyLoadedClass(string $fullyQualifiedClassName): int
    {
        $count = 0;
        foreach ($this->getLazyLoadedEntities() as $lazyLoaded) {
            if ($lazyLoaded['namespace'] . '\\' . $lazyLoaded['className'] === $fullyQualifiedClassName) {
                $count++;
            }
        }

        return $count;
    }

    public function getManagedEntities(): array
    {
        static $ordered = false;
        if ($ordered === false) {
            arsort($this->data['managedEntities']);
            $ordered = true;
        }

        return $this->data['managedEntities'];
    }

    public function countManagedEntities(): int
    {
        $return = 0;
        foreach ($this->getManagedEntities() as $stats) {
            $return += $stats['count'];
        }

        return $return;
    }

    public function getHydrationTotalTime(): float
    {
        $return = 0;
        foreach ($this->data['hydrationTimes'] as $times) {
            foreach ($times as $time) {
                $return += $time['time'];
            }
        }

        return round($return, 2);
    }

    public function getHydrationTimesByHydrator(): array
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

    public function getHydrationTimePercent(): float
    {
        return $this->getDoctrineTime() > 0
            ? round(($this->getHydrationTotalTime() * 100) / $this->getDoctrineTime())
            : 0;
    }

    public function getDoctrineTime(): float
    {
        return round($this->getQueriesTime() + $this->getHydrationTotalTime(), 2);
    }

    public function getQueriesAlert(): int
    {
        return $this->data['queriesAlert'];
    }

    public function getManagedEntitiesAlert(): int
    {
        return $this->data['managedEntitiesAlert'];
    }

    public function getLazyLoadedEntitiesAlert(): int
    {
        return $this->data['lazyLoadedEntitiesAlert'];
    }

    public function getHydrationTimealert(): int
    {
        return $this->data['hydrationTimeAlert'];
    }

    public function getToolbarStatus(): ?string
    {
        $alert =
            $this->countQueries() >= $this->getQueriesAlert()
            || $this->countManagedEntities() >= $this->data['managedEntitiesAlert']
            || $this->countLazyLoadedEntities() >= $this->data['lazyLoadedEntitiesAlert']
            || $this->getHydrationTotalTime() >= $this->data['hydrationTimeAlert'];

        return $alert ? 'red' : null;
    }

    public function isHydratorsOverloaded(): bool
    {
        return $this->countQueries() === 0 || ($this->countQueries() > 0 && count($this->data['hydrationTimes']) > 0);
    }

    public function getHydratedEntities(string $hydrator): array
    {
        return array_key_exists($hydrator, $this->data['hydratedEntities'])
            ? $this->data['hydratedEntities'][$hydrator]
            : [];
    }

    public function countHydratedEntities(string $hydrator): int
    {
        $count = 0;
        foreach ($this->getHydratedEntities($hydrator) as $classes) {
            foreach ($classes as $identifiers) {
                $count += count($identifiers);
            }
        }

        return $count;
    }

    public function explodeClassParts(string $fullyClassifiedClassName): array
    {
        $posBackSlash = strrpos($fullyClassifiedClassName, '\\');

        return [
            'namespace' => substr($fullyClassifiedClassName, 0, $posBackSlash),
            'className' => substr($fullyClassifiedClassName, $posBackSlash + 1)
        ];
    }

    public function mergeIdentifiers(array $identifiers): array
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

    public function identifiersAsString(array $identifiers): string
    {
        $return = [];
        foreach ($identifiers as $name => $value) {
            $return[] = $name . ': ' . $value;
        }

        return implode(', ', $return);
    }

    protected function parseManagedEntities(): array
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
