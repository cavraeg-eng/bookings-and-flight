<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts\components\api\ApiResponseObject;

class Flight extends ApiResponseObject
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
    /**
     * @var FlightDetail[]
     */
    public $details;
    /**
     * @var FlightStop[]
     */
    public $stops;

}
