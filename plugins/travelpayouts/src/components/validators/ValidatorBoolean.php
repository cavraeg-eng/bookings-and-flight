<?php

namespace Travelpayouts\components\validators;

use Travelpayouts;

/**
 * InlineValidator represents a validator which is defined as a method in the object being validated.
 * The validation method must have the following signature:
 * ```php
 * function foo($attribute, $params, $validator)
 * ```
 * where `$attribute` refers to the name of the attribute being validated, while `$params` is an array representing the
 * additional parameters supplied in the validation rule. Parameter `$validator` refers to the related
 * [[InlineValidator]] object and is available since version 2.0.11.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ValidatorBoolean extends Validator
{
    /**
     * @var mixed the value representing true status. Defaults to '1'.
     */
    public $trueValue = '1';
    /**
     * @var mixed the value representing false status. Defaults to '0'.
     */
    public $falseValue = '0';
    /**
     * @var bool whether the comparison to [[trueValue]] and [[falseValue]] is strict.
     * When this is true, the attribute value and type must both match those of [[trueValue]] or [[falseValue]].
     * Defaults to false, meaning only the value needs to be matched.
     */
    public $strict = false;
    /**
     * @var string
     */
    public $message;


    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Travelpayouts::__('The {attribute} must be either "{true}" or "{false}"');
        }
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    public function trueValue($value)
    {
        $this->trueValue = $value;
        return $this;
    }

    public function falseValue($value)
    {
        $this->falseValue = $value;
        return $this;
    }

    public function strict($value)
    {
        $this->strict = $value;
        return $this;
    }

    protected function validateValue($value)
    {
        if ($this->strict) {
            $valid = $value === $this->trueValue || $value === $this->falseValue;
        } else {
            $valid = $value == $this->trueValue || $value == $this->falseValue;
        }
        if (!$valid) {
            return [$this->message, [
                'true' => $this->trueValue === true ? 'true' : $this->trueValue,
                'false' => $this->falseValue === false ? 'false' : $this->falseValue,
            ]];
        }
    }
}
