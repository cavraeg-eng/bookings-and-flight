<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Exception;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\traits\GetterSetterTrait;

class BaseObject
{
    use GetterSetterTrait;

    public function __construct($config = [])
    {
        if (!empty($config) && is_array($config)) {
            self::configure($this, $config);
        }
        $this->init();
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
    }

    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
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
     * @param $type
     * @param array $params
     * @return mixed
     * @throws InvalidConfigException
     */
    public static function createObject($type, array $params = [])
    {
        if (is_string($type)) {
            return new $type($params);
        }
        if (is_callable($type, true)) {
            return call_user_func_array($type,$params);
        }

        if (!is_array($type)) {
            throw new InvalidConfigException('Unsupported configuration type: ' . gettype($type));
        }

        if (isset($type['__class'])) {
            $class = $type['__class'];
            unset($type['__class'], $type['class']);
            return new $class($type);
        }

        if (isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);
            return new $class($type);
        }

        throw new InvalidConfigException('Object configuration must be an array containing a "class" or "__class" element.');
    }
}
