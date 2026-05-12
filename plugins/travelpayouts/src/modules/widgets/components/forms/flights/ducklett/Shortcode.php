<?php

namespace Travelpayouts\modules\widgets\components\forms\flights\ducklett;

use Travelpayouts\components\shortcodes\BaseShortcode;
use Travelpayouts\components\ShortcodesTagHelper;

class Shortcode extends BaseShortcode
{
    public function generate()
    {
        $attributes = $this->attributes;

        if (
            isset($attributes['filter']) &&
            !empty($attributes['filter']) &&
            $attributes['filter'] == Widget::FILTER_BY_ROUTE
        ) {
            if (isset($attributes['airlines'])) {
                unset($attributes['airlines']);
            }
        } else {
            //$attributes['filter'] = Widget::FILTER_BY_AIRLINE;
            $attributes['airline'] = $attributes['airlines'];

            unset($attributes['airlines']);

            if (isset($attributes['origin'])) {
                unset($attributes['origin']);
            }

            if (isset($attributes['destination'])) {
                unset($attributes['destination']);
            }
        }

        return ShortcodesTagHelper::selfClosing($this->id, $attributes);
    }
}