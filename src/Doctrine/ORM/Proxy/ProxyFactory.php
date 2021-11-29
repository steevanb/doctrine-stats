<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Proxy;

use Doctrine\Common\Proxy\ProxyDefinition;
use Doctrine\ORM\{
    EntityManagerInterface,
    Persisters\Entity\EntityPersister,
    Proxy\Proxy,
    Proxy\ProxyFactory as DoctrineProxyFactory
};
use Steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;

class ProxyFactory extends DoctrineProxyFactory
{
    /** @param class-string $className */
    protected function createProxyDefinition($className): ProxyDefinition
    {
        $proxyDefinition = parent::createProxyDefinition($className);

        $doctrineInitializer = $proxyDefinition->initializer;
        // Doctrine\ORM\Proxy\ProxyFactory::$uow is private, so we use Reflection to get it
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), 'uow');
        $reflectionProperty->setAccessible(true);
        $entityPersister = $reflectionProperty->getValue($this)->getEntityPersister($className);
        $reflectionProperty->setAccessible(false);

        $initializer = function (Proxy $proxy) use ($doctrineInitializer, $entityPersister) {
            $entityManager = $this->getEntityManager($entityPersister);

            call_user_func($doctrineInitializer, $proxy);

            $postLazyLoadEventArgs = new PostLazyLoadEventArgs($entityManager, $proxy);
            $entityManager->getEventManager()->dispatchEvent(PostLazyLoadEventArgs::EVENT_NAME, $postLazyLoadEventArgs);
        };

        $proxyDefinition->initializer = $initializer;

        return $proxyDefinition;
    }

    protected function getEntityManager(EntityPersister $entityPersister): EntityManagerInterface
    {
        $property = new \ReflectionProperty(get_class($entityPersister), 'em');
        $property->setAccessible(true);
        /** @var EntityManagerInterface $return */
        $return = $property->getValue($entityPersister);
        $property->setAccessible(false);

        return $return;
    }
}
