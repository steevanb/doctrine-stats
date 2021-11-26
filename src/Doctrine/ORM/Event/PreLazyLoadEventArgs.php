<?php

declare(strict_types=1);

namespace Steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;

class PreLazyLoadEventArgs extends EventArgs
{
    public const EVENT_NAME = 'preLazyLoad';

    /** @var string */
    protected $eventId;

    public function __construct()
    {
        $this->eventId = uniqid('ladyLoad_');
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }
}
