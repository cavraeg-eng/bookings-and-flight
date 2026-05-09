<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\base\dictionary;

/**
 * Class EmptyItem
 * @package Travelpayouts\components\base\dictionary
 * Пустой метод обертка для несуществующего значения из словаря, на любой запрос присылает null
 */
class EmptyItem
{
    public function __get($name)
    {
        return null;
    }

    public function __set($name, $value)
    {

    }

    public function __isset($name)
    {
        return false;
    }

    public function __call($name, $arguments)
    {
        return null;
    }
}
