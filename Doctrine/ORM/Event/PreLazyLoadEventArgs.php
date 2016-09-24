<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PreLazyLoadEventArgs extends EventArgs
{
    const EVENT_NAME = 'preLazyLoad';

    /** @var string */
    protected $eventId;

    public function __construct()
    {
        $this->eventId = uniqid('ladyLoad_');
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->eventId;
    }
}
