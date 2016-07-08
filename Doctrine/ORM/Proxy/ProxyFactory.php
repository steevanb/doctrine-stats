<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Proxy;

use Doctrine\Common\Proxy\ProxyDefinition;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\Proxy\ProxyFactory as DoctrineProxyFactory;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadingEventArgs;

class ProxyFactory extends DoctrineProxyFactory
{
    /**
     * @param string $className
     * @return ProxyDefinition
     */
    protected function createProxyDefinition($className)
    {
        $proxyDefinition = parent::createProxyDefinition($className);

        $doctrineInitializer = $proxyDefinition->initializer;
        // Doctrine\ORM\Proxy\ProxyFactory::$uow is private, so use Reflection to get it
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), 'uow');
        $reflectionProperty->setAccessible(true);
        $entityPersister = $reflectionProperty->getValue($this)->getEntityPersister($className);
        $reflectionProperty->setAccessible(false);

        $initializer = function(Proxy $proxy) use ($doctrineInitializer, $entityPersister) {
            call_user_func($doctrineInitializer, $proxy);

            // Doctrine\ORM\Proxy\ProxyFactory::$em is private, so use Reflection to get it
            $property = new \ReflectionProperty(get_class($entityPersister), 'em');
            $property->setAccessible(true);
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $property->getValue($entityPersister);
            $property->setAccessible(false);
            $eventArgs = new PostLazyLoadingEventArgs($entityManager, $proxy);
            $entityManager->getEventManager()->dispatchEvent(PostLazyLoadingEventArgs::EVENT_NAME, $eventArgs);
        };

        $proxyDefinition->initializer = $initializer;

        return $proxyDefinition;
    }
}
