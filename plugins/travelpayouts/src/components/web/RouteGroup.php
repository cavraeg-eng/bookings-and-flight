<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\Vendor\FastRoute\RouteCollector;
use Travelpayouts\includes\Router;

class RouteGroup extends BaseRouteCollection
{

    /**
     * @Inject
     * @var Router
     */
    protected $router;

    /**
     * @inheritDoc
     */
    protected function registerCollection()
    {
        $this->registerRouterCollection($this->router);
    }

    /**
     * Региструруем роуты с использованием указанного роутера
     * @param Router $router
     * @return void
     */
    protected function registerRouterCollection(Router $router): void
    {
        $router->addGroup($this->prefix, function (RouteCollector $r) {
            foreach ($this->_actions as $action) {
                if ($action->route === '/') {
                    $action->route = '';
                }
                $r->addRoute($action->method, $action->route, function (...$args) use ($action) {
                    $parameters = $this->mergeParameters($action->getRouteParams(), $args);
                    $controller = $this->controller;
                    $controller->render($controller->runAction($action, $parameters));
                });
            }
        });
    }

}
