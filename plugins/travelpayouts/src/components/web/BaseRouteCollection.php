<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\components\BaseInjectedObject;

abstract class BaseRouteCollection extends BaseInjectedObject implements IRouteCollection
{
    /**
     * @var string
     */
    public $prefix;

    /**
     * @var Controller
     */
    public $controller;

    /**
     * @var boolean
     */
    protected $isRegistered;

    /**
     * @var Action[]
     */
    protected $_actions = [];

    public function addAction(Action $action)
    {
        $this->_actions = array_merge($this->_actions, [$action]);
    }

    public function registerRoutes()
    {
        if ($this->isRegistered) {
            return;
        }
        $this->beforeRegister();
        $this->registerCollection();
        $this->isRegistered = true;
        $this->afterRegister();
    }

    /**
     * @return mixed
     */
    abstract protected function registerCollection();

    protected function beforeRegister()
    {
    }

    protected function afterRegister()
    {
    }

    protected function mergeParameters($keys, $values)
    {
        $result = [];

        foreach ($keys as $index => $key) {
            if (isset($values[$index])) {
                $result[$key] = $values[$index];
            }
        }
        return $result;
    }

}
