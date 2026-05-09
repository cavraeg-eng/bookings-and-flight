<?php

namespace Travelpayouts\modules\links;

use Travelpayouts\components\Module;
use Travelpayouts\components\shortcodes\ShortcodeHelper;
use Travelpayouts\modules\links\components\LegacyLinkShortcode;

class Links extends Module
{
    /**
     * Список классов шорткодов подлежащих регистрации
     * @var string[]
     */
    protected $shortcodeList = [
        LegacyLinkShortcode::class,
        \Travelpayouts\modules\links\components\flights\Shortcode::class,
        \Travelpayouts\modules\links\components\hotels\Shortcode::class
    ];

    /**
     * Регистрация шорткодов указанных в shortcodeList
     * @see $shortcodeList
     */
    public function registerShortcodes()
    {
        ShortcodeHelper::registerShortcodeList($this->shortcodeList);
    }
}
