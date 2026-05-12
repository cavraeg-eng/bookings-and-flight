<?php

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix;

use Travelpayouts\components\api\ApiResponseObject;

class PriceCalendarMatrixApiResponse extends ApiResponseObject
{
    /**
     * @var int
     */
    public $value;
    /**
     * @var int
     */
    public $trip_class;
    /**
     * @var bool
     */
    public $show_to_affiliates;
    /**
     * @var string
     */
    public $origin;
    /**
     * @var string
     */
    public $destination;
    /**
     * @var string
     */
    public $gate;
    /**
     * @var string
     */
    public $depart_date;
    /**
     * @var string
     */
    public $return_date;
    /**
     * @var int
     */
    public $number_of_changes;
    /**
     * @var string
     */
    public $found_at;
    /**
     * @var int
     */
    public $duration;
    /**
     * @var int
     */
    public $distance;
    /**
     * @var bool
     */
    public $actual;
}
