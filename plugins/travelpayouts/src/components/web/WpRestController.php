<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

abstract class WpRestController extends Controller
{
    public $responseClass = JsonResponse::class;
    protected $routeCollectionClass = WpRestRouteGroup::class;
}
