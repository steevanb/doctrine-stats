<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PostCreateEntityEventArgs extends EventArgs
{
    public const EVENT_NAME = 'postCreateEntity';

    /** @var string */
    protected $hydratorClassName;

    /** @var string */
    protected $className;

    /** @var array */
    protected $classIdentifiers = [];

    public function __construct(string $hydratorClassName, string $className, array $classIdentifiers)
    {
        $this->hydratorClassName = $hydratorClassName;
        $this->className = $className;
        $this->classIdentifiers = $classIdentifiers;
    }

    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassIdentifiers(): array
    {
        return $this->classIdentifiers;
    }
}
