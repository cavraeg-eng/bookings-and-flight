<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts\components\api\ApiResponseObject;

class FlightDirect extends ApiResponseObject
{
    /**
     * @var string
     */
    public $depart_time;
    /**
     * @var string
     */
    public $arrival_time;
    /**
     * @var int
     */
    public $arrival_day_indicator;
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
    /**
     * @var boolean[]
     */
    public $op_days;
    /**
     * @var string
     */
    public $choose_dates_url;
    /**
     * @var string
     */
    public $origin_iata;
    /**
     * @var string
     */
    public $destination_iata;
}
