<?php

namespace Travelpayouts\components;

class ShortcodesTagHelper
{
    public static function selfClosing($shortcode, $attributes)
    {
        $shortcodeArray = self::shortcode($shortcode, $attributes);

        return self::wrap(
            implode(' ', $shortcodeArray)
        );
    }


    public static function enclosing($shortcode, $attributes, $content = '')
    {
        $shortcodeArray = self::shortcode($shortcode, $attributes);

        $shortcode = self::wrap(implode(' ', $shortcodeArray));
        $shortcode .= $content;
        $shortcode .= self::wrap('/' . $shortcode);

        return $shortcode;
    }

    private static function shortcode($shortcode, $attributes)
    {
        $shortcodeArray = [$shortcode];

        foreach ($attributes as $name => $value) {
            if ($value !== null) {
                $shortcodeArray[] = self::attr($name, $value);
            }
        }

        return $shortcodeArray;
    }


    private static function wrap($shortcode)
    {
        return '[' . $shortcode . ']';
    }

    private static function attr($name, $value)
    {
        if (is_bool($value)) {
            $value = json_encode($value);
        }

        return $name . '=' . '"' . $value . '"';
    }
}
