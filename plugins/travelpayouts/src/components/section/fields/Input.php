<?php

namespace Travelpayouts\components\section\fields;

class Input extends BaseField
{
    public $type = 'text';
    public $placeholder;
    public $default = '';
    public $secret = false;
    public $autocomplete;

    /**
     * @param string $value
     * @return $this
     */
    public function setType(string $value): Input
    {
        $this->type = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setSecret(bool $value = true): Input
    {
        $this->secret = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAutocomplete(string $value): Input
    {
        $this->autocomplete = $value;

        return $this;
    }

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
