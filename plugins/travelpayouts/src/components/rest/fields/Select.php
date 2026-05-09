<?php

namespace Travelpayouts\components\rest\fields;

class Select extends BaseValueField
{
    public $type = 'selectbox';
    /**
     * @var array
     */
    public $options;
    /**
     * @var string
     */
    public $default = '';
    /**
     * @var string
     */
    public $placeholder;

    /**
     * @param array $value
     * @return $this
     */
    public function setOptions($value)
    {
        if (is_array($value)) {
            $this->options = $value;
        }

        return $this;
    }

    /**
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

}
