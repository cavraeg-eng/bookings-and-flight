<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\sortBy\SortByField;

class SortBy extends Select
{
    public $id = 'sort_by';
    public $type = SortByField::TYPE;

    public function setData($options, $default): SortBy
    {
        if (isset($options['placebo'])) {
            unset($options['placebo']);
        }

        $this->setOptions($options);

        if (array_key_exists($default, $options)) {
            $this->default = $default;
        } else {
            $this->default = array_key_first($options);
        }

        return $this;

    }
}