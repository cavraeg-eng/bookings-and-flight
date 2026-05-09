<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

class WpRestRouteGroup extends BaseRouteCollection
{

    /**
     * @inheritDoc
     */
    protected function registerCollection()
    {
        add_action('rest_api_init', function () {
            foreach ($this->_actions as $action) {
                if ($action->route === '/') {
                    $action->route = '';
                }

                $parsedRoute = $action->getParsedRoute();
                register_rest_route($this->prefix, $this->normalizeRoute($parsedRoute), [
                    'methods' => $action->method,
                    'callback' => function (\WP_REST_Request $request) use ($action) {
                        $controller = $this->controller;
                        $controller->render($controller->runAction($action, $request->get_params()));
                    },
                    'permission_callback' => function () {
                        return is_user_logged_in() && current_user_can('edit_posts');
                    },

                ]);
            }
        });
    }

    /**
     * Приводим роут из формата PhpFastRoute к формату совсместимому с wp rest
     * @param $parsedRoute
     * @return false|string
     */
    protected function normalizeRoute($parsedRoute)
    {
        $result = [];
        foreach ($parsedRoute as $routePart) {
            if (is_string($routePart)) {
                $result[] = $routePart;
            }
            if (is_array($routePart)) {
                list($attributeName, $expression) = $routePart;
                $result[] = "(?P<$attributeName>$expression)";
            }
        }
        // отрезаем слеш в начале строки
        return substr(implode('', $result), 1);
    }

}
