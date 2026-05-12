<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

abstract class PublicJsonController extends JsonController
{
    /**
     * @var string
     */
    protected $routeCollectionClass = RoutePublicGroup::class;
}
