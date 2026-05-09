<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\actions;

use Travelpayouts;
use Travelpayouts\components\web\CheckAccessAction;

class GetAccessTokenAction extends CheckAccessAction
{
    public function run(): array
    {
        return [
            'access_token' => Travelpayouts::getInstance()->account->getToken(),
        ];
    }

}