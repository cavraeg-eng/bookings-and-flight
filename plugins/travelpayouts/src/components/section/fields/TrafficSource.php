<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\platformSelect\PlatformSelectField;

class TrafficSource extends Select
{
    public $type = PlatformSelectField::TYPE;
    public $default = '0';
}