<?php

namespace Travelpayouts\components\shortcodes;

abstract class BaseShortcode
{
    public $id;
    public $attributes;

    public function __construct($id, $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }

    abstract public function generate();
}