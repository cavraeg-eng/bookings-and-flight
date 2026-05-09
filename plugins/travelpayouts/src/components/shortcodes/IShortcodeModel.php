<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\shortcodes;


interface IShortcodeModel
{
    /**
     * Массив с списком тегов, по которым будут зарегистрированы шорткоды
     * @return string[]
     */
    public static function shortcodeTags();

    /**
     * @param array $attributes
     * @param null $content
     * @param string $tag
     * @return string
     */
    public static function render_shortcode_static($attributes = [], $content = null, $tag = '');

    /**
     * @return bool
     */
    public static function isActive(): bool;
}
