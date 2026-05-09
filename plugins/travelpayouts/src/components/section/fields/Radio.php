<?php

namespace Travelpayouts\components\section\fields;

class Radio extends Select
{
    public $type = 'radio';
    public $multi_layout = 'full';
    public $select2 = null;

    /**
     * @param $value
     * @return $this
     */
    public function setMultiLayout($value): Radio
    {
        $this->multi_layout = $value;

        return $this;
    }
}