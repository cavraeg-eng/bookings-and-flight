<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts\components\api\ApiResponseObject;

class MinFlightDuration extends ApiResponseObject
{
    /**
     * @var int
     */
    public $days;
    /**
     * @var int
     */
    public $hours;
    /**
     * @var int
     */
    public $min;

}
