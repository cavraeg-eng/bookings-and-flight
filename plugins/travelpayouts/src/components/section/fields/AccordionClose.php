<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\OscAccordionField;

class AccordionClose extends BaseField
{
    public $type = OscAccordionField::TYPE;
    public $id = 'we';
    public $position = 'end';
    public $class = 'travelpayouts-destroy';
    public $hideTitle = true;
    public $wrapField = false;

    public $skipSave  = true;
}