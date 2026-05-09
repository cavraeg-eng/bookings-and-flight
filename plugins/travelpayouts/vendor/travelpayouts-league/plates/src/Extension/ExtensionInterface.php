<?php

namespace Travelpayouts\Vendor\League\Plates\Extension;
use Travelpayouts\Vendor\League\Plates\Engine;

/**
 * A common interface for extensions.
 */
interface ExtensionInterface
{
    public function register(Engine $engine);
}
