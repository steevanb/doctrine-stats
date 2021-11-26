<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PreHydrationEventArgs extends EventArgs
{
    public const EVENT_NAME = 'preHydration';

    /** @var string */
    protected $eventId;

    /** @var string */
    protected $hydratorClassName;

    public function __construct(string $hydratorClassName)
    {
        $this->eventId = uniqid('hydration_');
        $this->hydratorClassName = $hydratorClassName;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getHydratorClassName(): string
    {
        return $this->hydratorClassName;
    }
}
