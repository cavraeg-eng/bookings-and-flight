<?php

namespace Travelpayouts\components\section\fields;

class Textarea extends BaseField
{
    public $type = 'textarea';
    public $rows = 6;

    /**
     * @param $value
     * @return $this
     */
    public function setRows($value): Textarea
    {
        $this->rows = $value;

        return $this;
    }
}