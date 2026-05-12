<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\traits;

trait SingletonTrait
{
    protected static $_instances = [];

    /**
     * @param bool $refresh
     * @return self
     */
    public static function getInstance($refresh = false)
    {
        $className = static::class;

        if ($refresh || !isset(self::$_instances[$className])) {
            self::$_instances[$className] = new $className;
        }
        return self::$_instances[$className];
    }

    final public function __wakeup()
    {
    }

    final public function __clone()
    {
    }
}
