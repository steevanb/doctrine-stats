<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Proxy;

use Doctrine\Common\Proxy\ProxyDefinition;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\Proxy\ProxyFactory as DoctrineProxyFactory;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PostLazyLoadEventArgs;
use steevanb\DoctrineStats\Doctrine\ORM\Event\PreLazyLoadEventArgs;

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
            // Doctrine\ORM\Proxy\ProxyFactory::$em is private, so use Reflection to get it
            $property = new \ReflectionProperty(get_class($entityPersister), 'em');
            $property->setAccessible(true);
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $property->getValue($entityPersister);
            $property->setAccessible(false);

            $preLazyLoadEventArgs = new PreLazyLoadEventArgs();
            $entityManager->getEventManager()->dispatchEvent(PreLazyLoadEventArgs::EVENT_NAME, $preLazyLoadEventArgs);

            call_user_func($doctrineInitializer, $proxy);

            $postLazyLoadEventArgs = new PostLazyLoadEventArgs(
                $entityManager,
                $proxy,
                $preLazyLoadEventArgs->getEventId()
            );
            $entityManager->getEventManager()->dispatchEvent(PostLazyLoadEventArgs::EVENT_NAME, $postLazyLoadEventArgs);
        };

        $proxyDefinition->initializer = $initializer;

        return $proxyDefinition;
    }
}
