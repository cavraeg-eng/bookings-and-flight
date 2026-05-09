<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\InjectedModel;

abstract class Controller extends InjectedModel
{
    /**
     * @var string
     */
    protected $routerPath;

    /**
     * @var string
     */
    protected $responseClass = Response::class;

    /**
     * @var string
     */
    protected $routeCollectionClass = RouteGroup::class;

    /**
     * @var Response
     */
    protected $_response;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->checkInstance('routeCollectionClass', $this->routeCollectionClass, BaseRouteCollection::class);
        $this->checkInstance('responseClass', $this->responseClass, Response::class);
        $this->registerActions();
    }

    /**
     * Список действий контроллера
     * @return Action[] | array[]
     */
    abstract public function actions();

    protected function registerActions()
    {
        $routeGroup = $this->getRouteCollection();
        foreach ($this->actions() as $routePath => $actionParams) {
            $action = null;
            if (is_array($actionParams)) {
                $action = isset($actionParams['class'])
                    ? new $actionParams['class']($this, $actionParams)
                    : new ControllerAction($this, $actionParams);

                if (!$action instanceof Action) {
                    throw new InvalidConfigException(get_class($this) . ': Action must be extend RouteAction');
                }
            }
            if ($actionParams instanceof Action) {
                $action = $actionParams;
            }
            if ($action) {
                $action->controller = $this;
                if (!$action->route && is_string($routePath)) {
                    $action->route = '/' . $routePath;
                }
                $routeGroup->addAction($action);
            }
        }

        $routeGroup->registerRoutes();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->_response) {
            $this->_response = new $this->responseClass();
        }
        return $this->_response;
    }

    /**
     * @return string
     */
    public function getRoutePath()
    {
        if (!$this->routerPath) {
            $name = (new \ReflectionClass($this))->getShortName();
            $this->routerPath = strtolower(str_replace('Controller', '', $name));
        }
        return $this->routerPath;
    }

    public function runAction(Action $action, $params = [])
    {

        $result = null;
        if ($this->beforeAction($action)) {
            try {
                $result = $action->runWithParams($params);
            } catch (\Exception $e) {
                $result = $e;
            }
            $result = $this->afterAction($action, $result);
        }
        return $result;
    }

    public function render($result)
    {
        $response = $this->getResponse();
        if ($result instanceof \Exception) {
            $response->setStatusCodeByException($result);
        } else {
            $response->content = $result;
        }
        $response->send();
    }

    /**
     * @param Action $action
     * @return bool
     */
    public function beforeAction(Action $action)
    {
        return true;
    }

    /**
     * @param Action $action
     * @param $result
     * @return mixed;
     */
    public function afterAction(Action $action, $result)
    {
        return $result;
    }

    protected function checkInstance($property, $actual, $expected)
    {
        if (!is_a($actual, $expected, true)) {
            throw new InvalidConfigException(get_class($this) . " $property must be instance of $expected");
        }
    }

    /**
     * @return IRouteCollection
     */
    protected function getRouteCollection()
    {
        return new $this->routeCollectionClass([
            'prefix' => $this->getRoutePath(),
            'controller' => $this,
        ]);
    }
}
