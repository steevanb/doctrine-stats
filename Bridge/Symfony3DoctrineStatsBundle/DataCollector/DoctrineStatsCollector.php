<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\DataCollector;

use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector
{
    /** @var array */
    protected $lazyLoadedEntities = array();

    /** @var SQLLogger */
    protected $sqlLogger;

    /** @var array */
    protected $managerEntities = array();

    /**
     * @param DebugStack $sqlLogger
     */
    public function __construct(DebugStack $sqlLogger)
    {
        $this->sqlLogger = $sqlLogger;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine_stats';
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
                        array('field' => $field)
                    );
                }
            }
        }

        $this->lazyLoadedEntities[] = array_merge(
            $this->explodeClassParts($classMetaData->name),
            array(
                'identifiers' => $classMetaData->getIdentifierValues($entity),
                'associations' => $associations
            )
        );

        return $this;
    }

    /**
     * @param string $className
     * @param string $identifiers
     * @return $this
     */
    public function addManagedEntity($className, $identifiers)
    {
        if (array_key_exists($className, $this->managerEntities) === false) {
            $this->managerEntities[$className] = array();
        }
        $this->managerEntities[$className][] = $identifiers;

        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'lazyLoadedEntities' => $this->lazyLoadedEntities,
            'queries' => $this->sqlLogger->queries,
            'managedEntities' => $this->managerEntities
        );
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        static $return = false;

        if ($return === false) {
            $return = array();
            foreach ($this->data['queries'] as $query) {
                if (array_key_exists($query['sql'], $return) === false) {
                    $return[$query['sql']] = array('executionMS' => 0, 'data' => array());
                }
                $return[$query['sql']]['executionMS'] += $query['executionMS'];
                $return[$query['sql']]['data'][] = array(
                    'params' => $query['params']
                );
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
            uasort($this->data['managedEntities'], function ($managedA, $managedB) {
                return $managedA > $managedB ? -1 : 1;
            });
            $ordered = true;
        }

        return $this->data['managedEntities'];
    }

    /**
     * @return int
     */
    public function countManagedEntities()
    {
        $count = 0;
        foreach ($this->getManagedEntities() as $managedEntity) {
            $count += count($managedEntity);
        }

        return $count;
    }

    /**
     * @param $fullyClassifiedClassName
     * @return array
     */
    protected function explodeClassParts($fullyClassifiedClassName)
    {
        $posBackSlash = strrpos($fullyClassifiedClassName, '\\');

        return array(
            'namespace' => substr($fullyClassifiedClassName, 0, $posBackSlash),
            'className' => substr($fullyClassifiedClassName, $posBackSlash + 1)
        );
    }
}
