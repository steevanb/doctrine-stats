<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\{
    Configuration,
    EntityManager as DoctrineEntityManager,
    ORMException
};
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Steevanb\DoctrineStats\Doctrine\ORM\Proxy\ProxyFactory;

class EntityManager extends DoctrineEntityManager
{
    /**
     * Copied from Doctrine\ORM\EntityManager, cause return use new EntityManager() instead of new static()
     *
     * @param mixed $conn
     * @return EntityManager
     */
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if ($config->getMetadataDriverImpl() instanceof MappingDriver === false) {
            throw ORMException::missingMappingDriverImpl();
        }

        switch (true) {
            case (is_array($conn)):
                $conn = \Doctrine\DBAL\DriverManager::getConnection(
                    $conn,
                    $config,
                    ($eventManager ?: new EventManager())
                );
                break;

            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                    throw ORMException::mismatchedEventManager();
                }
                break;

            default:
                throw new \InvalidArgumentException('Invalid argument: ' . $conn);
        }

        return new static($conn, $config, $conn->getEventManager());
    }

    protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
    {
        parent::__construct($conn, $config, $eventManager);

        $this->setParentPrivatePropertyValue(
            'proxyFactory',
            new ProxyFactory(
                $this,
                $config->getProxyDir(),
                $config->getProxyNamespace(),
                $config->getAutoGenerateProxyClasses()
            )
        );
        $this->setParentPrivatePropertyValue('unitOfWork', new UnitOfWork($this));
    }

    /** @param mixed $value */
    protected function setParentPrivatePropertyValue(string $name, $value): self
    {
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), $name);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this, $value);
        $reflectionProperty->setAccessible(false);

        return $this;
    }
}
