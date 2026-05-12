<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;
use Travelpayouts\Vendor\FastRoute\RouteParser\Std;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\helpers\ArrayHelper;

/**
 * @property-read Response $response
 */
class Action extends BaseInjectedObject
{
    /**
     * @var string| string[]
     */
    public $method;

    /**
     * @var string
     */
    public $route;

    /**
     * @var Controller
     */
    public $controller;

    public function __construct(Controller $controller, $config = [])
    {
        parent::__construct($config);
        $this->controller = $controller;
    }

    public function runWithParams($params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }

        if ($this->beforeRun($params)) {
            $result = call_user_func_array([$this, 'run'], array_values($params));
            $this->afterRun($params);

            return $result;
        }
        return null;
    }

    public function beforeRun($params)
    {
        return true;
    }

    public function afterRun($params)
    {

    }

    /**
     * @param string|string[] $method
     * @return self
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $route
     * @return self
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getParsedRoute()
    {
        $parsedRoute = (new Std())->parse($this->route);
        return !empty($parsedRoute) ? ArrayHelper::getFirst($parsedRoute) : null;
    }

    public function getRouteParams()
    {
        $parsedRoute = $this->getParsedRoute();
        $result = [];
        if ($parsedRoute) {
            foreach ($parsedRoute as $routePart) {
                if (is_array($routePart)) {
                    $result[] = $routePart[0];
                }
            }
        }
        return $result;
    }

    /**
     * @return Response
     */
    protected function getResponse()
    {
        return $this->controller->getResponse();
    }

}
