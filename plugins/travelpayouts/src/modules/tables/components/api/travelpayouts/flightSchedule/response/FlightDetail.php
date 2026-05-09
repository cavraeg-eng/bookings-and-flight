<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts\components\api\ApiResponseObject;

class FlightDetail extends ApiResponseObject
{
    /**
     * @var string
     */
    public $airline_logo;
    /**
     * @var string
     */
    public $airline_code;
    /**
     * @var string
     */
    public $airline_name;
    /**
     * @var int
     */
    public $flight_number;
}
