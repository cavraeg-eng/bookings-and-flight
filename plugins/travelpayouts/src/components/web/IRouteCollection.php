<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

interface IRouteCollection
{
    public function addAction(Action $action);

    public function registerRoutes();
}
