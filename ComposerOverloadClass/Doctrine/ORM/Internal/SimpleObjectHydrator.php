<?php

namespace Doctrine\ORM\Internal\Hydration;

use steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class SimpleObjectHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\SimpleObjectHydrator
{
    use OverloadedHydratorTrait;
}
