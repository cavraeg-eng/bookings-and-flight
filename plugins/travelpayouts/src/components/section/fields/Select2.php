<?php

namespace Travelpayouts\components\section\fields;

class Select2 extends BaseField
{
    public $select2 = [
        'theme' => 'travelpayouts',
        'allowClear' => false,
        'minimumResultsForSearch' => 10,
    ];

    /**
     * @param $options
     * @return $this
     */
    public function setSelect2($options): Select2
    {
        $this->select2 = $options;

        return $this;
    }
}