<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\components\exceptions\HttpException;

abstract class CheckAccessAction extends Action
{
    /**
     * @var callable
     */
    public $checkAccess;

    public function beforeRun($params)
    {
        if ($this->checkAccess && !call_user_func_array($this->checkAccess, array_values($params))) {
            throw new HttpException(403);
        }

        return parent::beforeRun($params);
    }

    /**
     * @param callable $checkAccess
     * @return self
     */
    public function setCheckAccess($checkAccess)
    {
        $this->checkAccess = $checkAccess;
        return $this;
    }

}
