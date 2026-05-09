<?php

namespace Travelpayouts\components\rest\fields;

class InputNumber extends Input
{
    public $type = 'input-number';
    public $minimum = 1;
    public $maximum = 999;

    /**
     * @param int|float $value
     * @return $this
     */
    public function setMinimum($value)
    {
        if (is_numeric($value)) {
            $this->minimum = $value;
        }

        return $this;
    }

    /**
     * @param int|float $value
     * @return $this
     */
    public function setMaximum($value): self
    {
        if (is_numeric($value)) {
            $this->maximum = $value;
        }

        return $this;
    }
}
