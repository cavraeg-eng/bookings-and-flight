<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\module;

use Travelpayouts\admin\redux\ReduxHooks;

interface IModuleRedux
{
    /**
     * Добавляем секцию в Redux
     * @return void
     * @see ReduxHooks::registerModuleSections()
     */
    public function registerSection();
}
