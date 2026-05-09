<?php

namespace Travelpayouts\components\rest\fields;

class Input extends BaseValueField
{
    public $type = 'input';
    public $placeholder;
    public $default = '';

    /**
     * @param string $value
     * @return $this
     */
    public function setPlaceholder($value)
    {
        if (is_string($value)) {
            $this->placeholder = $value;
        }

        return $this;
    }
}
