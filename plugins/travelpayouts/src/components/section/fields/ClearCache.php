<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\clearTableCache\ClearTableCacheField;

class ClearCache extends BaseField
{
    public $type = ClearTableCacheField::TYPE;
}