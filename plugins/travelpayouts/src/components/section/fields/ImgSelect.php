<?php

namespace Travelpayouts\components\section\fields;

class ImgSelect extends Select
{
    public $type = 'image_select';
    public $select2 = null;
    public $height = 250;

    /**
     * @param $value
     * @return $this
     */
    public function setHeight($value): ImgSelect
    {
        $this->height = $value;

        return $this;
    }
}