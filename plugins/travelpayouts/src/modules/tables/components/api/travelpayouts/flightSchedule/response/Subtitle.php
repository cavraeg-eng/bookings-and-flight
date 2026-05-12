<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

class Subtitle extends \Travelpayouts\components\api\ApiResponseObject
{
    /**
     * @var FlightStop
     */
    public $origin;
    /**
     * @var FlightStop
     */
    public $destination;
}
