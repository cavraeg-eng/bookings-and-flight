<?php

namespace Travelpayouts\components\section\fields;

class Raw extends BaseField
{
    public $type = 'raw';
    public $content;
    public $full_width = false;

    /**
     * @param $value
     * @return $this
     */
    public function setContent($value): Raw
    {
        $this->content = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setFullWidth(bool $value): Raw
    {
        $this->full_width = $value;

        return $this;
    }
}