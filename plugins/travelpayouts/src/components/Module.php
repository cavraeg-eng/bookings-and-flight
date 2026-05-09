<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

/**
 * Class Module
 * @package Travelpayouts\components
 */
abstract class Module extends BaseInjectedObject
{
    public $name;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }
}
