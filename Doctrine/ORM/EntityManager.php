<?php

namespace steevanb\DoctrineStats\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\ORMException;
use steevanb\DoctrineStats\Doctrine\ORM\Proxy\ProxyFactory;

class EntityManager extends DoctrineEntityManager
{
    /**
     * Copied from Doctrine\ORM\EntityManager, cause return use new EntityManager() instead of new static()
     *
     * @param mixed $conn
     * @param Configuration $config
     * @param EventManager|null $eventManager
     * @return EntityManager
     * @throws ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        if ( ! $config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }

        switch (true) {
            case (is_array($conn)):
                $conn = \Doctrine\DBAL\DriverManager::getConnection(
                    $conn, $config, ($eventManager ?: new EventManager())
                );
                break;

            case ($conn instanceof Connection):
                if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                    throw ORMException::mismatchedEventManager();
                }
                break;

            default:
                throw new \InvalidArgumentException("Invalid argument: " . $conn);
        }

        return new static($conn, $config, $conn->getEventManager());
    }

    /**
     * @param Connection $conn
     * @param Configuration $config
     * @param EventManager $eventManager
     */
    protected function __construct(Connection $conn, Configuration $config, EventManager $eventManager)
    {
        parent::__construct($conn, $config, $eventManager);

        $this->setParentPrivatePropertyValue('proxyFactory', new ProxyFactory(
            $this,
            $config->getProxyDir(),
            $config->getProxyNamespace(),
            $config->getAutoGenerateProxyClasses()
        ));
        $this->setParentPrivatePropertyValue('unitOfWork', new UnitOfWork($this));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setParentPrivatePropertyValue($name, $value)
    {
        $reflectionProperty = new \ReflectionProperty(get_parent_class($this), $name);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this, $value);
        $reflectionProperty->setAccessible(false);

        return $this;
    }
}
