<?php

namespace Travelpayouts\components\section\fields;

class Select extends Select2
{
    public $type = 'select';
    public $options = [];

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options): Select
    {
        $this->options = $options;

        return $this;
    }
}