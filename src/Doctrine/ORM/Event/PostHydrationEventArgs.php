<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PostHydrationEventArgs extends EventArgs
{
    public const EVENT_NAME = 'postHydration';

    /** @var string */
    protected $preHydrationEventId;

    /** @var class-string */
    protected $hydratorClassName;

    /** @param class-string $hydratorClassName */
    public function __construct(string $preHydrationEventId, string $hydratorClassName)
    {
        $this->preHydrationEventId = $preHydrationEventId;
        $this->hydratorClassName = $hydratorClassName;
    }

    public function getPreHydrationEventId(): string
    {
        return $this->preHydrationEventId;
    }

    /** @return class-string */
    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }
}
