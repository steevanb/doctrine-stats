<?php

namespace Doctrine\ORM\Internal\Hydration;

use steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class ScalarHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\ScalarHydrator
{
    use OverloadedHydratorTrait;
}
