<?php

namespace Travelpayouts\components\section\fields;

class Slider extends BaseField
{
    public $type = 'slider';
    public $display_value = 'text';
    public $min;
    public $step = 1;
    public $max;

    /**
     * @param $value
     * @return $this
     */
    public function setDisplayValue($value): Slider
    {
        $this->display_value = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMin($value): Slider
    {
        $this->min = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMax($value): Slider
    {
        $this->max = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setStep($value): Slider
    {
        $this->step = $value;

        return $this;
    }
}