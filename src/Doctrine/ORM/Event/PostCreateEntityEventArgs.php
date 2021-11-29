<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PostCreateEntityEventArgs extends EventArgs
{
    public const EVENT_NAME = 'postCreateEntity';

    /** @var class-string */
    protected $hydratorClassName;

    /** @var class-string */
    protected $className;

    /** @var array<string|int, string> */
    protected $classIdentifiers = [];

    /**
     * @param class-string $hydratorClassName
     * @param class-string $className
     * @param array<string|int, string> $classIdentifiers
     */
    public function __construct(string $hydratorClassName, string $className, array $classIdentifiers)
    {
        $this->hydratorClassName = $hydratorClassName;
        $this->className = $className;
        $this->classIdentifiers = $classIdentifiers;
    }

    /** @return class-string */
    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }

    /** @return class-string */
    public function getClassName(): string
    {
        return $this->className;
    }

    /** @return array<string|int, string> */
    public function getClassIdentifiers(): array
    {
        return $this->classIdentifiers;
    }
}
