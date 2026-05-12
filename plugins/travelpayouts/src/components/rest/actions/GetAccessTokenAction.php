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
        $token = Travelpayouts::getInstance()->account->getToken();

        return [
            'access_token' => '',
            'has_access_token' => !empty($token),
        ];
    }

}
