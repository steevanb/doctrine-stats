<?php

namespace Doctrine\ORM\Internal\Hydration;

use steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class ArrayHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\ArrayHydrator
{
    use OverloadedHydratorTrait;
}
