<?php

namespace Doctrine\ORM\Internal\Hydration;

use steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class ObjectHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\ObjectHydrator
{
    use OverloadedHydratorTrait;
}
