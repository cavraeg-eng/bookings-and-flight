<?php

namespace Travelpayouts\components\rest\fields;

class Text extends BaseField
{
    public $type = 'explanation-text';
    public $label;

    /**
     * @param string $value
     * @return $this
     */
    public function setLabel($value)
    {
        if (is_string($value)) {
            $this->label = $value;
        }

        return $this;
    }
}
