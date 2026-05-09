<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\traits;

trait GetterSetterTrait
{
    /**
     * Returns a property value.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using the following syntax to read a property
     * @param string $name the property name
     * @return mixed the property value
     * @see __set
     */
    public function __get($name)
    {
        $methodName = $this->getMethodName('get', $name);
        return $methodName
            ? $this->$methodName()
            : null;
    }

    /**
     * Sets value of a component property.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using the following syntax to set a property
     * @param string $name the property name
     * @param mixed $value the property value or callback
     * @see __get
     */
    public function __set($name, $value)
    {
        $methodName = $this->getMethodName('set', $name);
        if ($methodName) {
            $this->$methodName($value);
        }
    }

    /**
     * Checks if a property value is null.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using isset() to detect if a component property is set or not.
     * @param string $name the property name
     * @return boolean
     */
    public function __isset($name)
    {
        $methodName = $this->getMethodName('get', $name);
        return $methodName
            ? $this->$methodName() !== null
            : null;
    }

    /**
     * Sets a component property to be null.
     * Do not call this method. This is a PHP magic method that we override
     * to allow using unset() to set a component property to be null.
     * @param string $name the property name
     */
    public function __unset($name)
    {
        $this->__set($name, null);
    }

    protected function getMethodName($prefix, $name)
    {
        $methodNames = [
            // snake_case
            "{$prefix}_{$name}",
            // camelCase
            $prefix . ucfirst($name),
        ];

        foreach ($methodNames as $methodName) {
            if (method_exists($this, $methodName)) {
                return $methodName;
            }
        }
        return false;
    }
}
