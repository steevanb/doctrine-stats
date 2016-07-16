<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PostHydrationEventArgs extends EventArgs
{
    const EVENT_NAME = 'postHydration';

    /** @var string */
    protected $preHydrationEventId;

    /** @var string */
    protected $hydratorClassName;

    /**
     * @param string $preHydrationEventId
     * @param string $hydratorClassName
     */
    public function __construct($preHydrationEventId, $hydratorClassName)
    {
        $this->preHydrationEventId = $preHydrationEventId;
        $this->hydratorClassName = $hydratorClassName;
    }

    /**
     * @return string
     */
    public function getPreHydrationEventId()
    {
        return $this->preHydrationEventId;
    }

    /**
     * @return string
     */
    public function getHydratorClassName()
    {
        return $this->hydratorClassName;
    }
}
