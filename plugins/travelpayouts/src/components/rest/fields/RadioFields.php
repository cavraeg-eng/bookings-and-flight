<?php

namespace Travelpayouts\components\rest\fields;

use Travelpayouts\components\Model;

class RadioFields extends BaseValueField
{
    public $type = 'radio-with-fields';
    public $required = true;
    public $options = [];

    /**
     * @var Model
     */
    protected $model;

    public function addOption($id, $name, $values = [])
    {
        $this->options[] = [
            'id' => $id,
            'label' => $name,
            'fields' => $values,
        ];

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return RadioFields
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

}
