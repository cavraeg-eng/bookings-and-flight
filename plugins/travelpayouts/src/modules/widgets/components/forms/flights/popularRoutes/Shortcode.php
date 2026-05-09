<?php

namespace Travelpayouts\modules\widgets\components\forms\flights\popularRoutes;

use Travelpayouts\components\shortcodes\BaseShortcode;
use Travelpayouts\components\ShortcodesTagHelper;

class Shortcode extends BaseShortcode
{
    public function generate()
    {
        $destinations = [];
        $shortcodes = [];

        if(isset($this->attributes['destinations']) && !empty($this->attributes['destinations'])) {
            $destinations = $this->attributes['destinations'];
        }

        foreach ($destinations as $destination) {
            $shortcodes[] = $this->single($destination);
        }

        return $shortcodes;
    }

    private function single($destination)
    {
        $attributes = $this->attributes;
        unset($attributes['destinations']);
        $attributes['destination'] = $destination;

        return ShortcodesTagHelper::selfClosing($this->id, $attributes);
    }
}