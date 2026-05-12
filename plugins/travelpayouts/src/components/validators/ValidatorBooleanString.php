<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\validators;

class ValidatorBooleanString extends ValidatorBoolean
{
    public $trueValues = ['1', 'true', true, 1];

    public $falseValues = ['0', 'false', false, 0];

    protected function validateValue($value)
    {
        $valid = in_array($value, array_merge($this->trueValues, $this->falseValues), true);
        if (!$valid) {
            return [
                $this->message,
                [
                    'true' => $this->getValuesAsString($this->trueValues),
                    'false' => $this->getValuesAsString($this->falseValues),
                ],
            ];
        }
    }

    protected function getValuesAsString(array $values): string
    {
        return implode(',', array_filter($values, static function ($value) {
            return is_string($value);
        }));
    }

}
