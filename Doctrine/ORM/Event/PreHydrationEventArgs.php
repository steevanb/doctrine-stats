<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PreHydrationEventArgs extends EventArgs
{
    const EVENT_NAME = 'preHydration';

    /** @var string */
    protected $eventId;

    /** @var string */
    protected $hydratorClassName;

    /**
     * @param $hydratorClassName
     */
    public function __construct($hydratorClassName)
    {
        $this->eventId = uniqid('hydration_');
        $this->hydratorClassName = $hydratorClassName;
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getHydratorClassName()
    {
        return $this->hydratorClassName;
    }
}
