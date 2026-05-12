<?php

declare(strict_types=1);

namespace Travelpayouts\Vendor\DI;
use Travelpayouts\Vendor\Psr\Container\ContainerExceptionInterface;

/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
