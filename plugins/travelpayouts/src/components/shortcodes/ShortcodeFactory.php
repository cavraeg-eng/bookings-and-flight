<?php

namespace Travelpayouts\components\shortcodes;

use Travelpayouts\modules\widgets\components\forms\flights\popularRoutes\Shortcode as PopularRoutesShortcode;
use Travelpayouts\modules\widgets\components\forms\flights\ducklett\Shortcode as DucklettShortcode;

class ShortcodeFactory
{
    public static function getShortcode($id, $attributes)
    {
        switch ($id) {
            case 'tp_popular_routes_widget':
                return new PopularRoutesShortcode($id, $attributes);
            case 'tp_ducklett_widget':
                return new DucklettShortcode($id, $attributes);
            default:
                return new DefaultShortcode($id, $attributes);
        }
    }
}