<?php

namespace steevanb\DoctrineStats\Doctrine\ORM\Event;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Proxy\Proxy;

class PostLazyLoadingEventArgs extends EventArgs
{
    /** @var Proxy */
    protected $proxy;

    /**
     * @param Proxy $proxy
     */
    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }
}
