<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts\components\api\ApiResponseObject;

/**
 * @property-read $flights
 * @property-read $direct_flights
 */
class FlightScheduleApiResponse extends ApiResponseObject
{
    /**
     * @var Title
     */
    public $title;
    /**
     * @var Subtitle
     */
    public $subtitle;
    /**
     * @var FlightDirect[]|null
     */
    public $direct_flights;

    /**
     * @var Flight[]|null
     */
    public $flights;

}
