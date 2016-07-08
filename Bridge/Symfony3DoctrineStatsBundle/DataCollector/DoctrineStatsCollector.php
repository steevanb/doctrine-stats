<?php

namespace steevanb\DoctrineStats\Bridge\Symfony3DoctrineStatsBundle\DataCollector;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DoctrineStatsCollector extends DataCollector
{
    /** @var array */
    protected $lazyLoadedEntities = array();

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine_stats';
    }

    /**
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
     * @param Request $request
     * @param Response $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array('lazyLoadedEntities' => $this->lazyLoadedEntities);
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
