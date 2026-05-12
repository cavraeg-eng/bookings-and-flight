<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\shortcodes;

use Travelpayouts;


class ShortcodeHelper
{
    /**
     * @param string[] $shortcodeList
     */
    public static function registerShortcodeList($shortcodeList)
    {
        foreach ($shortcodeList as $shortcodeClass) {
            if (is_subclass_of($shortcodeClass, ShortcodeModel::class)) {
                $shortcodeClass::register();
            }
        }
    }

    public static function registerDisabledShortcode($tag)
    {
        Travelpayouts::getInstance()->hooksLoader->addShortcode(
            $tag,
            function () {
                return '';
            }
        );
    }
}
