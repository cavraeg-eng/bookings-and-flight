<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\includes\HooksLoader;

abstract class HookableObject extends BaseInjectedObject
{
    /**
     * @Inject
     * @var HooksLoader
     */
    private $hooksLoader;

    /**
     * Регистрируем хуки единожды
     */
    final public function setUpHooks()
    {
        if ($this->hooksLoader->isRegistered($this)) {
            $this->hookList($this->hooksLoader);
        }
    }

    /**
     * @return void
     */
    abstract protected function hookList(HooksLoader $hooksLoader);
}
