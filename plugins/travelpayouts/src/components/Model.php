<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Exception;
use IteratorAggregate;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\validators\Validator;
use Travelpayouts\interfaces\Arrayable;
use Travelpayouts\traits\ArrayableTrait;
use Travelpayouts\traits\SingletonTrait;

/**
 * Class Model
 * @package Travelpayouts\src\includes\base
 * @property array $attributes
 * @property $scenario
 * @property-read array $errors
 */
class Model extends BaseObject implements IteratorAggregate, ArrayAccess, Arrayable
{
	use ArrayableTrait;
	use SingletonTrait;

	/**
	 * The name of the default scenario.
	 */
	const SCENARIO_DEFAULT = 'default';

	/**
	 * @var array validation errors (attribute name => array of errors)
	 */
	private $_errors = [];
	/**
	 * @var ArrayObject list of validators
	 */
	private $_validators;
	/**
	 * @var string current scenario
	 */
	private $_scenario = self::SCENARIO_DEFAULT;

	public function rules()
	{
		return [];
	}

	/**
	 * Returns a value indicating whether there is any validation error.
	 * @param string $attribute attribute name. Use null to check all attributes.
	 * @return boolean whether there is any error.
	 */
	public function has_errors($attribute = null)
	{
		if ($attribute === null)
			return $this->_errors !== [];
		else
			return isset($this->_errors[$attribute]);
	}

	/**
	 * Returns the errors for all attribute or a single attribute.
	 * @param string $attribute attribute name. Use null to retrieve errors for all attributes.
	 * @return array errors for all attributes or the specified attribute. Empty array is returned if no error.
	 */
	public function getErrors($attribute = null)
	{
		if ($attribute === null)
			return $this->_errors;
		else
			return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
	}

	/**
	 * Returns the first error of the specified attribute.
	 * @param string $attribute attribute name.
	 * @return string the error message. Null is returned if no error.
	 */
	public function get_error($attribute)
	{
		return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
	}

	/**
	 * Adds a new error to the specified attribute.
	 * @param string $attribute attribute name
	 * @param string $error new error message
	 */
	public function add_error($attribute, $error)
	{
		$this->_errors[$attribute][] = $error;
	}

	/**
	 * Adds a list of errors.
	 * @param array $errors a list of errors. The array keys must be attribute names.
	 * The array values should be error messages. If an attribute has multiple errors,
	 * these errors must be given in terms of an array.
	 * You may use the result of {@link getErrors} as the value for this parameter.
	 */
	public function add_errors($errors)
	{
		foreach ($errors as $attribute => $error) {
			if (is_array($error)) {
				foreach ($error as $e)
					$this->add_error($attribute, $e);
			} else
				$this->add_error($attribute, $error);
		}
	}

	/**
	 * Removes errors for all attributes or a single attribute.
	 * @param string $attribute attribute name. Use null to remove errors for all attribute.
	 */
	public function clear_errors($attribute = null)
	{
		if ($attribute === null)
			$this->_errors = [];
		else
			unset($this->_errors[$attribute]);
	}

	/**
	 * Creates validator objects based on the validation rules specified in [[rules()]].
	 * Unlike [[get_validators()]], each time this method is called, a new list of validators will be returned.
	 * @return ArrayObject validators
	 * @throws Exception
	 */
	public function create_validators()
	{
        $validators = new ArrayObject();
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = Validator::createValidator($rule[1], $this, (array) $rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
	}

	/**
	 * Returns all the validators declared in [[rules()]].
	 * This method differs from [[get_active_validators()]] in that the latter
	 * only returns the validators applicable to the current [[scenario]].
	 * Because this method returns an ArrayObject object, you may
	 * manipulate it by inserting or removing validators.
	 * For example,
	 * ```php
	 * $model->validators[] = $newValidator;
	 * ```
	 * @return Validator[] all the validators declared in the model.
	 */
	public function get_validators()
	{
		if ($this->_validators === null) {
			$this->_validators = $this->create_validators();
		}

		return $this->_validators;
	}

	public function attribute_labels()
	{
		return [];
	}

	/**
	 * Returns the text label for the specified attribute.
	 * @param string $attribute the attribute name
	 * @return string the attribute label
	 * @see generate_attribute_label()
	 * @see attribute_labels()
	 */
	public function get_attribute_label($attribute)
	{
		$labels = $this->attribute_labels();
		return isset($labels[$attribute]) ? $labels[$attribute] : $this->generate_attribute_label($attribute);
	}

	/**
	 * Generates a user friendly attribute label based on the give attribute name.
	 * This is done by replacing underscores, dashes and dots with blanks and
	 * changing the first letter of each word to upper case.
	 * For example, 'department_name' or 'DepartmentName' will generate 'Department Name'.
	 * @param string $name the column name
	 * @return string the attribute label
	 */
	public function generate_attribute_label($name)
	{
		return Inflector::camel2words($name, true);
	}

    /**
	 * Returns the list of attribute names.
	 * By default, this method returns all public non-static properties of the class.
	 * You may override this method to change the default behavior.
	 * @return array list of attribute names.
	 * @throws ReflectionException
	 */
	public function attributes()
	{
		$class = new ReflectionClass($this);
		$names = [];
		foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
			if (!$property->isStatic()) {
				$names[] = $property->getName();
			}
		}
		return $names;
	}

	/**
	 * Returns attribute values.
	 * @param array $names list of attributes whose value needs to be returned.
	 * Defaults to null, meaning all attributes listed in [[attributes()]] will be returned.
	 * If it is an array, only the attributes in the array will be returned.
	 * @param array $except list of attributes whose value should NOT be returned.
	 * @return array attribute values (name => value).
	 * @throws ReflectionException
	 */
	public function get_attributes($names = null, $except = [])
	{
		$values = [];
		if ($names === null) {
			$names = $this->attributes();
		}
		foreach ($names as $name) {
			$values[$name] = $this->$name;
		}
		foreach ($except as $name) {
			unset($values[$name]);
		}

		return $values;
	}

	/**
	 * Sets the attribute values in a massive way.
	 * @param array $values attribute values (name => value) to be assigned to the model.
	 * @param bool $safeOnly whether the assignments should only be done to the safe attributes.
	 * A safe attribute is one that is associated with a validation rule in the current [[scenario]].
	 * @throws ReflectionException
	 * @see attributes()
	 * @see safeAttributes()
	 */
	public function set_attributes($values, $safeOnly = true)
	{
		if (is_array($values)) {
			$attributes = array_flip($safeOnly ? $this->safe_attributes() : $this->attributes());
			foreach ($values as $name => $value) {
				if (isset($attributes[$name])) {
					$this->$name = $value;
				}
			}
		}
	}

	/**
	 * Performs the data validation.
	 * This method executes the validation rules applicable to the current [[scenario]].
	 * The following criteria are used to determine whether a rule is currently applicable:
	 * - the rule must be associated with the attributes relevant to the current scenario;
	 * - the rules must be effective for the current scenario.
	 * This method will call [[beforeValidate()]] and [[afterValidate()]] before and
	 * after the actual validation, respectively. If [[beforeValidate()]] returns false,
	 * the validation will be cancelled and [[afterValidate()]] will not be called.
	 * Errors found during the validation can be retrieved via [[getErrors()]],
	 * [[getFirstErrors()]] and [[getFirstError()]].
	 * @param string[]|string $attributeNames attribute name or list of attribute names that should be validated.
	 * If this parameter is empty, it means any attribute listed in the applicable
	 * validation rules should be validated.
	 * @param bool $clearErrors whether to call [[clearErrors()]] before performing validation
	 * @return bool whether the validation is successful without any error.
	 * @throws Exception if the current scenario is unknown.
	 */
	public function validate($attributeNames = null, $clearErrors = true)
	{
		if ($clearErrors) {
			$this->clear_errors();
		}

		if (!$this->before_validate()) {
			return false;
		}

		$scenarios = $this->scenarios();
		$scenario = $this->get_scenario();

		if (!isset($scenarios[$scenario])) {
			throw new RuntimeException("Unknown scenario: $scenario");
		}

		if ($attributeNames === null) {
			$attributeNames = $this->active_attributes();
		}

		$attributeNames = (array)$attributeNames;
		/* @var $validator Validator */
		foreach ($this->get_active_validators() as $validator) {
			$validator->validateAttributes($this, $attributeNames);
		}
		$this->after_validate();

		return !$this->has_errors();
	}

	/**
	 * Returns the attribute names that are safe to be massively assigned in the current scenario.
	 * @return string[] safe attribute names
	 */
	public function safe_attributes()
	{
		$scenario = $this->get_scenario();
		$scenarios = $this->scenarios();
		if (!isset($scenarios[$scenario])) {
			return [];
		}

		$attributes = [];
		foreach ($scenarios[$scenario] as $attribute) {
			if ($attribute[0] !== '!' && !in_array('!' . $attribute, $scenarios[$scenario])) {
				$attributes[] = $attribute;
			}
		}
		return $attributes;
	}

	/**
	 * Returns a value indicating whether the attribute is safe for massive assignments.
	 * @param string $attribute attribute name
	 * @return bool whether the attribute is safe for massive assignments
	 * @see safeAttributes()
	 */
	public function is_attribute_safe($attribute)
	{
		return in_array($attribute, $this->safe_attributes(), true);
	}

	/**
	 * This method is invoked before validation starts.
	 * @return bool whether the validation should be executed. Defaults to true.
	 * If false is returned, the validation will stop and the model is considered invalid.
	 */
	public function before_validate()
	{
		return true;
	}

	/**
	 * This method is invoked after validation ends.
	 */
	public function after_validate()
	{
	}

	/**
	 * Returns a list of scenarios and the corresponding active attributes.
	 * An active attribute is one that is subject to validation in the current scenario.
	 * The returned array should be in the following format:
	 * ```php
	 * [
	 *     'scenario1' => ['attribute11', 'attribute12', ...],
	 *     'scenario2' => ['attribute21', 'attribute22', ...],
	 *     ...
	 * ]
	 * ```
	 * By default, an active attribute is considered safe and can be massively assigned.
	 * If an attribute should NOT be massively assigned (thus considered unsafe),
	 * please prefix the attribute with an exclamation character (e.g. `'!rank'`).
	 * The default implementation of this method will return all scenarios found in the [[rules()]]
	 * declaration. A special scenario named [[SCENARIO_DEFAULT]] will contain all attributes
	 * found in the [[rules()]]. Each scenario will be associated with the attributes that
	 * are being validated by the validation rules that apply to the scenario.
	 * @return array a list of scenarios and the corresponding active attributes.
	 */
	public function scenarios()
	{
		$scenarios = [self::SCENARIO_DEFAULT => []];
		foreach ($this->get_validators() as $validator) {
			foreach ($validator->on as $scenario) {
				$scenarios[$scenario] = [];
			}
			foreach ($validator->except as $scenario) {
				$scenarios[$scenario] = [];
			}
		}
		$names = array_keys($scenarios);
		foreach ($this->get_validators() as $validator) {
			if (empty($validator->on) && empty($validator->except)) {
				foreach ($names as $name) {
					foreach ($validator->attributes as $attribute) {
						$scenarios[$name][$attribute] = true;
					}
				}
			} elseif (empty($validator->on)) {
				foreach ($names as $name) {
					if (!in_array($name, $validator->except, true)) {
						foreach ($validator->attributes as $attribute) {
							$scenarios[$name][$attribute] = true;
						}
					}
				}
			} else {
				foreach ($validator->on as $name) {
					foreach ($validator->attributes as $attribute) {
						$scenarios[$name][$attribute] = true;
					}
				}
			}
		}
		foreach ($scenarios as $scenario => $attributes) {
			if (!empty($attributes)) {
				$scenarios[$scenario] = array_keys($attributes);
			}
		}
		return $scenarios;
	}

	/**
	 * Returns the scenario that this model is used in.
	 * Scenario affects how validation is performed and which attributes can
	 * be massively assigned.
	 * @return string the scenario that this model is in. Defaults to [[SCENARIO_DEFAULT]].
	 */
	public function get_scenario()
	{
		return $this->_scenario;
	}

	/**
	 * Sets the scenario for the model.
	 * Note that this method does not check if the scenario exists or not.
	 * The method [[validate()]] will perform this check.
	 * @param string $value the scenario that this model is in.
	 */
	public function set_scenario($value)
	{
		$this->_scenario = $value;
	}

	/**
	 * Returns the attribute names that are subject to validation in the current scenario.
	 * @return string[] safe attribute names
	 */
	public function active_attributes()
	{
		$scenario = $this->get_scenario();
		$scenarios = $this->scenarios();
		if (!isset($scenarios[$scenario])) {
			return [];
		}
		$attributes = array_keys(array_flip($scenarios[$scenario]));
		foreach ($attributes as $i => $attribute) {
			if ($attribute[0] === '!') {
				$attributes[$i] = substr($attribute, 1);
			}
		}

		return $attributes;
	}

	/**
	 * Returns the validators applicable to the current [[scenario]].
	 * @param string $attribute the name of the attribute whose applicable validators should be returned.
	 * If this is null, the validators for ALL attributes in the model will be returned.
	 * @return Validator[] the validators applicable to the current [[scenario]].
	 */
	public function get_active_validators($attribute = null)
	{
		$activeAttributes = $this->active_attributes();
		if ($attribute !== null && !in_array($attribute, $activeAttributes, true)) {
			return [];
		}
		$scenario = $this->get_scenario();
		$validators = [];
		foreach ($this->get_validators() as $validator) {
			if ($attribute === null) {
				$validatorAttributes = $validator->getValidationAttributes($activeAttributes);
				$attributeValid = !empty($validatorAttributes);
			} else {
				$attributeValid = in_array($attribute, $validator->getValidationAttributes($attribute), true);
			}
			if ($attributeValid && $validator->isActive($scenario)) {
				$validators[] = $validator;
			}
		}

		return $validators;
	}

	/**
	 * Returns the list of fields that should be returned by default by [[toArray()]] when no specific fields are
	 * specified. A field is a named element in the returned array by [[toArray()]]. This method should return an array
	 * of field names or field definitions. If the former, the field name will be treated as an object property name
	 * whose value will be used as the field value. If the latter, the array key should be the field name while the
	 * array value should be the corresponding field definition which can be either an object property name or a PHP
	 * callable returning the corresponding field value. The signature of the callable should be:
	 * ```php
	 * function ($model, $field) {
	 *     // return field value
	 * }
	 * ```
	 * For example, the following code declares four fields:
	 * - `email`: the field name is the same as the property name `email`;
	 * - `firstName` and `lastName`: the field names are `firstName` and `lastName`, and their
	 *   values are obtained from the `first_name` and `last_name` properties;
	 * - `fullName`: the field name is `fullName`. Its value is obtained by concatenating `first_name`
	 *   and `last_name`.
	 * ```php
	 * return [
	 *     'email',
	 *     'firstName' => 'first_name',
	 *     'lastName' => 'last_name',
	 *     'fullName' => function ($model) {
	 *         return $model->first_name . ' ' . $model->last_name;
	 *     },
	 * ];
	 * ```
	 * In this method, you may also want to return different lists of fields based on some context
	 * information. For example, depending on [[scenario]] or the privilege of the current application user,
	 * you may return different sets of visible fields or filter out some fields.
	 * The default implementation of this method returns [[attributes()]] indexed by the same attribute names.
	 * @return array the list of field names or field definitions.
	 * @throws ReflectionException
	 * @see toArray()
	 */
	public function fields()
	{
		$fields = $this->attributes();

		return array_combine($fields, $fields);
	}

	/**
	 * Returns an iterator for traversing the attributes in the model.
	 * This method is required by the interface [[\IteratorAggregate]].
	 * @return ArrayIterator an iterator for traversing the items in the list.
	 * @throws ReflectionException
	 */
    #[\ReturnTypeWillChange]
    public function getIterator()
	{
		$attributes = $this->get_attributes();
        return new ArrayIterator($attributes);
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `isset($model[$offset])`.
     * @param mixed $offset the offset to check on.
     * @return bool whether or not an offset exists.
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `$value = $model[$offset];`.
     * @param mixed $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `$model[$offset] = $item;`.
     * @param int $offset the offset to set element
     * @param mixed $item the element value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $item)
    {
        $this->$offset = $item;
    }

    /**
     * Sets the element value at the specified offset to null.
     * This method is required by the SPL interface [[\ArrayAccess]].
     * It is implicitly called when you use something like `unset($model[$offset])`.
     * @param mixed $offset the offset to unset element
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }
}
