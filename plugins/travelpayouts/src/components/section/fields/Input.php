<?php

namespace Travelpayouts\components\section\fields;

class Input extends BaseField
{
    public $type = 'text';
    public $placeholder;
    public $default = '';

    /**
     * @param $value
     * @return $this
     */
    public function setPlaceholder($value): Input
    {
        $this->placeholder = $value;
        $this->class .= ' input-with-placeholder';

        return $this;
    }
}