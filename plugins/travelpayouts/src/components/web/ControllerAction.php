<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\components\exceptions\InvalidConfigException;

class ControllerAction extends CheckAccessAction
{
    /**
     * @var callable
     */
    public $action;

    public function init()
    {
        if (!$this->action) {
            throw new InvalidConfigException(get_class($this) . ': You need to pass action attribute');
        }
    }

    /**
     * @param callable $action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function runWithParams($params)
    {
        if ($this->beforeRun($params)) {
            $result = call_user_func_array($this->action, array_values($params));
            $this->afterRun($params);
            return $result;
        }
        return null;
    }
}
