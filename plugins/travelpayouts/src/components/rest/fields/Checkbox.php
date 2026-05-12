<?php

namespace Travelpayouts\components\rest\fields;

class Checkbox extends BaseValueField
{
    public $type = 'checkbox';
    public $default = false;

    public function fields()
    {
        return array_merge(parent::fields(), [
            'default' => function () {
                return filter_var($this->default, FILTER_VALIDATE_BOOLEAN);
            },
        ]);
    }
}
