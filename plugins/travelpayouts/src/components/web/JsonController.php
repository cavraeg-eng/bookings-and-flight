<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

abstract class JsonController extends Controller
{
    public $responseClass = JsonResponse::class;
}
