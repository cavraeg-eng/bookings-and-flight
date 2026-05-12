<?php

namespace Travelpayouts\components\section\fields;

class Dimensions extends BaseField
{
    public $type = 'dimensions';
    public $units = ['px'];
    public $validate_callback;

    /**
     * @param $value
     * @return $this
     */
    public function setValidateCallback($value): Dimensions
    {
        $this->validate_callback = $value;

        return $this;
    }
}