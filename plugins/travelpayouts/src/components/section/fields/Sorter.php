<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\sorter\SorterField;

class Sorter extends BaseField
{
    public $type = SorterField::TYPE;
    public $columnsOptions = [];
    public $options = [];

    /**
     * @param $options
     * @return $this
     */
    public function setColumnOptions($options): Sorter
    {
        $this->columnsOptions = $options;

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options): Sorter
    {
        $this->options = $options;

        return $this;
    }
}