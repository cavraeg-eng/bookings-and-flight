<?php

namespace Travelpayouts\components\rest\fields;

class InputTag extends Input
{
    public $type = 'input_tag';
    public $delimiter;

    /**
     * @param string $value
     * @return $this
     */
    public function setDelimiter($value)
    {
        if (is_string($value)) {
            $this->delimiter = $value;
        }

        return $this;
    }
}
