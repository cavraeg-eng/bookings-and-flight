<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\OscAccordionField;

class AccordionOpen extends BaseField
{
    public $type = OscAccordionField::TYPE;
    public $id = 'ws';
    public $position = 'start';
    public $open = false;
    public $hideTitle = true;
    public $wrapField = false;
    public $class = 'travelpayouts-destroy';

    public $skipSave  = true;


    /**
     * @param bool $open
     * @return self
     */
    public function setIsOpen(bool $open): self
    {
        $this->open = $open;
        return $this;
    }
}