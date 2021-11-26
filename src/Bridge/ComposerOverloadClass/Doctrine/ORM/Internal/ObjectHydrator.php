<?php

declare(strict_types=1);

namespace Doctrine\ORM\Internal\Hydration;

use Steevanb\DoctrineStats\Doctrine\ORM\Event\OverloadedHydratorTrait;

class ObjectHydrator extends \ComposerOverloadClass\Doctrine\ORM\Internal\Hydration\ObjectHydrator
{
    use OverloadedHydratorTrait;
}
