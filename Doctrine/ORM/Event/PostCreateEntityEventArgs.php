<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PostCreateEntityEventArgs extends EventArgs
{
    const EVENT_NAME = 'postCreateEntity';

    /** @var string */
    protected $hydratorClassName;

    /** @var string */
    protected $className;

    /** @var array */
    protected $classIdentifiers = [];

    /**
     * @param $hydratorClassName
     * @param string $className
     * @param array $classIdentifiers
     */
    public function __construct($hydratorClassName, $className, array $classIdentifiers)
    {
        $this->hydratorClassName = $hydratorClassName;
        $this->className = $className;
        $this->classIdentifiers = $classIdentifiers;
    }

    /**
     * @return string
     */
    public function getHydratorClassName()
    {
        return $this->hydratorClassName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getClassIdentifiers()
    {
        return $this->classIdentifiers;
    }
}
