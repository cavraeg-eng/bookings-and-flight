<?php

namespace Travelpayouts\components\rest\fields;

class SelectAsync extends BaseValueField
{
    public $type = 'selectbox-async';
    public $default = '';
    public $placeholder;
    public $async;

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

    /**
     * @param array $value
     * @return $this
     */
    public function setAsync($value)
    {
        if (is_array($value)) {
            $this->async = $value;
        }

        return $this;
    }
}
