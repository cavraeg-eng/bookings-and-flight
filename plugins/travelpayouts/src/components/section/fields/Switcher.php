<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts;

class Switcher extends BaseField
{
    public $type = 'switch';
    public $on;
    public $off;

    public function init()
    {
        parent::init();

        $this->on = Travelpayouts::_x('on', 'admin.switcher');
        $this->off = Travelpayouts::_x('off', 'admin.switcher');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOn($value): Switcher
    {
        $this->on = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setOff($value): Switcher
    {
        $this->off = $value;

        return $this;
    }
}