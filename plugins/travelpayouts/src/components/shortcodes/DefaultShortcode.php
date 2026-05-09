<?php

namespace Travelpayouts\components\shortcodes;

use Travelpayouts\components\ShortcodesTagHelper;

class DefaultShortcode extends BaseShortcode
{
    public function generate()
    {
        return ShortcodesTagHelper::selfClosing($this->id, $this->attributes);
    }
}