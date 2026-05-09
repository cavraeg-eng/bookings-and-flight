<?php

namespace Travelpayouts\components\section\fields;

class Section extends BaseField
{
    public $type = 'section';
    public $indent = false;

    /**
     * @return $this
     */
    public function indent(): Section
    {
        $this->indent = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function notIndent(): Section
    {
        $this->indent = false;

        return $this;
    }
}