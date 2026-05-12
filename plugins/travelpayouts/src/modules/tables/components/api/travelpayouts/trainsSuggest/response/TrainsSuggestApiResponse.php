<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response;

use Travelpayouts\components\api\ApiResponseObject;

class TrainsSuggestApiResponse extends ApiResponseObject
{
    /**
     * @var Trip[]
     */
    public $trips = [];
    /**
     * @var string
     */
    public $url;
}
