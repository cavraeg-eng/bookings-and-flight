<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\Base;

use Exception;

class Component extends BaseObject
{
    /**
     * @param string $name the property name
     * @return mixed the property value or the value of a behavior's property
     * @throws Exception if the property is not defined
     * @throws Exception if the property is write-only.
     * @see __set()
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new Exception('Getting write-only property: ' . get_class($this) . '::' . $name);
        }

        throw new Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws Exception if the property is not defined
     * @throws Exception if the property is read-only.
     * @see __get()
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // set property
            $this->$setter($value);

            return;
        } elseif (strncmp($name, 'on ', 3) === 0) {
            // on event: attach event handler
            $this->on(trim(substr($name, 3)), $value);

            return;
        }

        if (method_exists($this, 'get' . $name)) {
            throw new Exception('Setting read-only property: ' . get_class($this) . '::' . $name);
        }

        throw new Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * @param string $name the property name or the event name
     * @return bool whether the named property is set
     * @see https://secure.php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        }
        return false;
    }

    /**
     * @param string $name the property name
     * @throws Exception if the property is read only.
     * @see https://secure.php.net/manual/en/function.unset.php
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
            return;
        }

        throw new Exception('Unsetting an unknown or read-only property: ' . get_class($this) . '::' . $name);
    }

    /**
     * @param string $name the method name
     * @param array $params method parameters
     * @return mixed the method return value
     * @throws Exception when calling unknown method
     */
    public function __call($name, $params)
    {
        throw new Exception('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true)
    {
        if (method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name the property name
     * @return bool whether the method is defined
     */
    public function hasMethod($name)
    {
        if (method_exists($this, $name)) {
            return true;
        }
        return false;
    }

}
