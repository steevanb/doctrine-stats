<?php

namespace Doctrine\ORM\Internal\Hydration;

use steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class SingleScalarHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\SingleScalarHydrator
{
    use OverloadedHydratorTrait;
}
