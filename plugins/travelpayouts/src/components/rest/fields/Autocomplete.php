<?php

namespace Travelpayouts\components\rest\fields;

class Autocomplete extends Input
{
    public $allowClear = false;
    public $async;
    public $type = 'input-autocomplete';
    public $placeholder = '';

    /**
     * @param boolean $value
     * @return $this
     */
    public function setAllowClear($value)
    {
        if (is_bool($value)) {
            $this->allowClear = $value;
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
