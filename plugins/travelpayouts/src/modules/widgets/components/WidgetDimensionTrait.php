<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components;
/**
 * Trait WidgetDimension
 * @package Travelpayouts\src\modules\widgets\components
 * @property string $width
 * @property string $height
 */
trait WidgetDimensionTrait
{
    protected $_width;
    protected $_height;

    public function get_width()
    {
        return $this->_width;
    }

    public function set_width($value)
    {
        if ($value) {
            $this->_width = $this->add_pixels($value);
        }
    }


    public function get_height()
    {
        return $this->_height;
    }

    public function set_height($value)
    {
        if ($value) {
            $this->_height = $this->add_pixels($value);
        }
    }


    public function set_responsive($value)
    {
        if ($value) {
            $this->_width = null;
        }
    }
}
