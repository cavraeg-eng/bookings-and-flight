<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components;


use Travelpayouts\components\widgets\WidgetModel;

abstract class BaseWidgetShortcodeModel extends WidgetModel
{
    /**
     * @inheritdoc
     */
    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $shortcodeModel = new static();
        $shortcodeModel->tag = $tag;
        $shortcodeModel->attributes = $attributes;
        if (isset($attributes['debug'])) {
            $shortcodeModel->debug = $attributes['debug'];
        }
        return $shortcodeModel->render();
    }
}
