<?php

namespace Travelpayouts\components\rest\fields;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\Model;

abstract class BaseField extends Model
{
    public $type;

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('Type attribute must be set');
        }
    }

    public function fields()
    {
        return $this->attributes();
    }
}
