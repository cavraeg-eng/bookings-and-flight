<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\helpers;


use ArrayAccess;
use Closure;
use Exception;
use Travelpayouts\components\exceptions\InvalidArgumentException;
use Travelpayouts\interfaces\Arrayable;

class ArrayHelper extends \Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Arrays\ArrayHelper
{
    /***
     * Полифил для метода array_key_first
     * @param $array
     * @return int|string|null
     */
    public static function getFirstKey($array)
    {
        foreach ($array as $key => $unused) {
            return $key;
        }
        return null;
    }

    /**
     * Returns a value indicating whether the given array is an associative array.
     * An array is associative if all its keys are strings. If `$allStrings` is false,
     * then an array will be treated as associative if at least one of its keys is a string.
     * Note that an empty array will NOT be considered associative.
     * @param array $array the array being checked
     * @param bool $allStrings whether the array keys must be all strings in order for
     * the array to be treated as associative.
     * @return bool whether the array is associative
     */
    public static function isAssociative($array, $allStrings = true)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }

        if ($allStrings) {
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }

            return true;
        }

        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a value indicating whether the given array is an indexed array.
     * An array is indexed if all its keys are integers. If `$consecutive` is true,
     * then the array keys must be a consecutive sequence starting from 0.
     * Note that an empty array will be considered indexed.
     * @param array $array the array being checked
     * @param bool $consecutive whether the array keys must be a consecutive sequence
     * in order for the array to be treated as indexed.
     * @return bool whether the array is indexed
     */
    public static function isIndexed($array, $consecutive = false)
    {
        if (!is_array($array)) {
            return false;
        }

        if (empty($array)) {
            return true;
        }

        if ($consecutive) {
            return array_keys($array) === range(0, count($array) - 1);
        }

        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $array
     * @param string $delimiter
     * @param string $prefix
     * @return array
     */
    public static function flatten($array, $delimiter = '.', $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix)
                    ? ''
                    : $delimiter) . $key;
            if (is_array($value) && self::isAssociative($value)) {
                $result = array_merge($result, self::flatten($value, $delimiter, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $result;
    }

    /**
     * Находим первое значение из array_filter
     * @param array $input
     * @param null $callback
     * @param int $flag
     * @return mixed|null
     * @see array_filter()
     */
    public static function find(array $input, $callback = null, $flag = 0)
    {
        if (is_array($input) && is_callable($callback)) {
            $foundValues = array_filter($input, $callback, $flag);
            return count($foundValues)
                ? array_shift($foundValues)
                : null;
        }
        return null;
    }

    /**
     * @param mixed[]
     * @return mixed|null
     */
    public static function getFirst($array)
    {
        if (is_array($array) && count($array)) {
            $newArray = self::cloneArray($array);
            return array_shift($newArray);
        }
        return null;
    }

    /**
     * @param mixed[]
     * @return mixed|null
     */
    public static function getLast($array)
    {
        if(is_array($array) && count($array)){
            return self::getFirst(array_slice($array, -1, 1, true));
        }
        return null;
    }


    /**
     * Получаем значение из массива и приводим его к boolean
     * @param $array
     * @param $key
     * @return bool
     */
    public static function getBooleanValue($array, $key)
    {
        return $array && isset($array[$key])
            ? StringHelper::toBoolean($array[$key])
            : false;
    }

    /**
     * Фильтруем пустые значения
     * @param $array
     * @return array
     */
    public static function filterEmpty($array)
    {
        return array_filter($array, function ($value) {
            return !is_null($value) && $value !== '';
        });
    }

	/**
	 * Recursively merge two config arrays with a specific behavior:
	 *
	 * 1. scalar values are overridden
	 * 2. array values are extended uniquely if all keys are numeric
	 * 3. all other array values are merged
	 *
	 * @param array $original
	 * @param array $override
	 * @return array
	 * @see http://stackoverflow.com/a/36366886/6812729
	 */
	public static function mergeRecursive(array $original, array $override)
	{
		foreach ($override as $key => $value) {
			if (isset($original[$key])) {
				if (!is_array($original[$key])) {
					if (is_numeric($key)) {
						// Append scalar value
						$original[] = $value;
					} else {
						// Override scalar value
						$original[$key] = $value;
					}
				} elseif (array_keys($original[$key]) === range(0, count($original[$key]) - 1)) {
					// Uniquely append to array with numeric keys
					$original[$key] = array_unique(array_merge($original[$key], $value));
				} else {
					// Merge all other arrays
					$original[$key] = self::mergeRecursive($original[$key], $value);
				}
			} else {
				// Simply add new key/value
				$original[$key] = $value;
			}
		}

		return $original;
	}

	/**
	 * Converts an object or an array of objects into an array.
	 * @param object|array|string $object the object to be converted into an array
	 * @param array $properties a mapping from object class names to the properties that need to put into the resulting arrays.
	 * The properties specified for each class is an array of the following format:
	 *
	 * ```php
	 * [
	 *     'app\models\Post' => [
	 *         'id',
	 *         'title',
	 *         // the key name in array result => property name
	 *         'createTime' => 'created_at',
	 *         // the key name in array result => anonymous function
	 *         'length' => function ($post) {
	 *             return strlen($post->content);
	 *         },
	 *     ],
	 * ]
	 * ```
	 *
	 * The result of `ArrayHelper::toArray($post, $properties)` could be like the following:
	 *
	 * ```php
	 * [
	 *     'id' => 123,
	 *     'title' => 'test',
	 *     'createTime' => '2013-01-01 12:00AM',
	 *     'length' => 301,
	 * ]
	 * ```
	 *
	 * @param bool $recursive whether to recursively converts properties which are objects into arrays.
	 * @return array the array representation of the object
	 */
	public static function toArray($object, $properties = [], $recursive = true)
	{
		if (is_array($object)) {
			if ($recursive) {
				foreach ($object as $key => $value) {
					if (is_array($value) || is_object($value)) {
						$object[$key] = static::toArray($value, $properties, true);
					}
				}
			}

			return $object;
		} elseif (is_object($object)) {
			if (!empty($properties)) {
				$className = get_class($object);
				if (!empty($properties[$className])) {
					$result = [];
					foreach ($properties[$className] as $key => $name) {
						if (is_int($key)) {
							$result[$name] = $object->$name;
						} else {
							$result[$key] = static::getValue($object, $name);
						}
					}

					return $recursive ? static::toArray($result, $properties) : $result;
				}
			}
			if ($object instanceof Arrayable) {
				$result = $object->toArray([], [], $recursive);
			} else {
				$result = [];
				foreach ($object as $key => $value) {
					$result[$key] = $value;
				}
			}

			return $recursive ? static::toArray($result, $properties) : $result;
		}

		return [$object];
	}

	/**
	 * Retrieves the value of an array element or object property with the given key or property name.
	 * If the key does not exist in the array, the default value will be returned instead.
	 * Not used when getting value from an object.
	 * The key may be specified in a dot format to retrieve the value of a sub-array or the property
	 * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
	 * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
	 * or `$array->x` is neither an array nor an object, the default value will be returned.
	 * Note that if the array already has an element `x.y.z`, then its value will be returned
	 * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
	 * like `['x', 'y', 'z']`.
	 * Below are some usage examples,
	 * ```php
	 * // working with array
	 * $username = \Travelpayouts\helpers\ArrayHelper::getValue($_POST, 'username');
	 * // working with object
	 * $username = \Travelpayouts\helpers\ArrayHelper::getValue($user, 'username');
	 * // working with anonymous function
	 * $fullName = \Travelpayouts\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
	 *     return $user->firstName . ' ' . $user->lastName;
	 * });
	 * // using dot format to retrieve the property of embedded object
	 * $street = \Travelpayouts\helpers\ArrayHelper::getValue($users, 'address.street');
	 * // using an array of keys to retrieve the value
	 * $value = \Travelpayouts\helpers\ArrayHelper::getValue($versions, ['1.0', 'date']);
	 * ```
	 * @param array|object $array array or object to extract value from
	 * @param string|Closure|array $key key name of the array element, an array of keys or property name of the object,
	 * or an anonymous function returning the value. The anonymous function signature should be:
	 * `function($array, $defaultValue)`.
	 * The possibility to pass an array of keys is available since version 2.0.4.
	 * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
	 * getting value from an object.
	 * @return mixed the value of the element if found, default value otherwise
	 * @throws Exception
	 */
	public static function getValue($array, $key, $default = null)
	{
		if ($key instanceof Closure) {
			return $key($array, $default);
		}

		if (is_array($key)) {
			$lastKey = array_pop($key);
			foreach ($key as $keyPart) {
				$array = static::getValue($array, $keyPart);
			}
			$key = $lastKey;
		}

		if (is_object($array) && property_exists($array, $key)) {
			return $array->$key;
		}

		if (static::keyExists($key, $array)) {
			return $array[$key];
		}

		if (($pos = strrpos($key, '.')) !== false) {
			$array = static::getValue($array, substr($key, 0, $pos), $default);
			$key = substr($key, $pos + 1);
		}

		if (static::keyExists($key, $array)) {
			return $array[$key];
		}
		if (is_object($array)) {
			// this is expected to fail if the property does not exist, or __get() is not implemented
			// it is not reliably possible to check whether a property is accessible beforehand
			try {
				return $array->$key;
			} catch (Exception $e) {
				if ($array instanceof ArrayAccess) {
					return $default;
				}
				throw $e;
			}
		}

		return $default;
	}

	/**
	 * Checks if the given array contains the specified key.
	 * This method enhances the `array_key_exists()` function by supporting case-insensitive
	 * key comparison.
	 * @param string $key the key to check
	 * @param array|ArrayAccess $array the array with keys to check
	 * @param bool $caseSensitive whether the key comparison should be case-sensitive
	 * @return bool whether the array contains the specified key
	 * @throws Exception
	 */
	public static function keyExists($key, $array, $caseSensitive = true)
	{
		if ($caseSensitive) {
			// Function `isset` checks key faster but skips `null`, `array_key_exists` handles this case
			// https://secure.php.net/manual/en/function.array-key-exists.php#107786
			if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
				return true;
			}
			// Cannot use `array_has_key` on Objects for PHP 7.4+, therefore we need to check using [[ArrayAccess::offsetExists()]]
			return $array instanceof ArrayAccess && $array->offsetExists($key);
		}

		if ($array instanceof ArrayAccess) {
			throw new Exception('Second parameter($array) cannot be ArrayAccess in case insensitive mode');
		}

		foreach (array_keys($array) as $k) {
			if (strcasecmp($key, $k) === 0) {
				return true;
			}
		}

		return false;
	}

    public static function addItem($array, $data)
    {
        return array_merge($array, [
            $data,
        ]);
	}

    public static function cloneArray($value)
    {
        return is_array($value) ? array_merge([], $value) : [];
    }

    /**
     * Sorts an array of objects or arrays (with the same structure) by one or several keys.
     * @param array $array the array to be sorted. The array will be modified after calling this method.
     * @param string|\Closure|array $key the key(s) to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](https://www.php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have
     * correct number of elements as that of $key.
     */
    public static function multisort(&$array, $key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $keys = is_array($key) ? $key : [$key];
        if (empty($keys) || empty($array)) {
            return;
        }
        $n = count($keys);
        if (is_scalar($direction)) {
            $direction = array_fill(0, $n, $direction);
        } elseif (count($direction) !== $n) {
            throw new InvalidArgumentException('The length of $direction parameter must be the same as that of $keys.');
        }
        if (is_scalar($sortFlag)) {
            $sortFlag = array_fill(0, $n, $sortFlag);
        } elseif (count($sortFlag) !== $n) {
            throw new InvalidArgumentException('The length of $sortFlag parameter must be the same as that of $keys.');
        }
        $args = [];
        foreach ($keys as $i => $k) {
            $flag = $sortFlag[$i];
            $args[] = static::getColumn($array, $k);
            $args[] = $direction[$i];
            $args[] = $flag;
        }

        // This fix is used for cases when main sorting specified by columns has equal values
        // Without it is will lead to Fatal Error: Nesting level too deep - recursive dependency?
        $args[] = range(1, count($array));
        $args[] = SORT_ASC;
        $args[] = SORT_NUMERIC;

        $args[] = &$array;
        call_user_func_array('array_multisort', $args);
    }

    /**
     * Recursive transform array into object
     * @param array $array
     * @return object
     */
    public static function toObject(array $array): object
    {
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $array[$key] = self::toObject($value);
            }
        }
        return (object)$array;
    }

}
