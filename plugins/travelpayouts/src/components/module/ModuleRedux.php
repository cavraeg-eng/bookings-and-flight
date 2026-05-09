<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\module;

use Travelpayouts\components\Module;
use Travelpayouts\components\shortcodes\ShortcodeHelper;

abstract class ModuleRedux extends Module implements IModuleRedux
{
    /**
     * Список классов шорткодов подлежащих регистрации
     * @var string[]
     */
    protected $shortcodeList = [];

    /**
     * Регистрация шорткодов указанных в shortcodeList
     * @see $shortcodeList
     */
    public function registerShortcodes()
    {
        ShortcodeHelper::registerShortcodeList($this->shortcodeList);
    }
}
