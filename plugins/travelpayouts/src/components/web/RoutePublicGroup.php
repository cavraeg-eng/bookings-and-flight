<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\includes\PublicRouter;

class RoutePublicGroup extends RouteGroup
{
    /**
     * @Inject
     * @var PublicRouter
     */
    protected $publicRouter;

    protected function registerCollection()
    {
        // Регистрируем роуты для админов
        parent::registerCollection();
        // Регистрируем публичные роуты
        $this->registerRouterCollection($this->publicRouter);
    }
}
