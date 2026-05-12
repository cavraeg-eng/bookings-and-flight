<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Travelpayouts\traits\GetterSetterTrait;

/**
 * Class Component
 * Base class that's appending getter/setter methods to another classes
 */
abstract class Component
{
    use GetterSetterTrait;
}
