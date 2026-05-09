<?php

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest;

use Travelpayouts\components\api\ApiResponseObject;

class PriceLatestApiResponse extends ApiResponseObject
{
    /**
     * @var int
     */
    public $value;

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
    public $depart_date;
    /**
     * @var string
     */
    public $return_date;

    /**
     * @var string
     */
    public $distance;

    /**
     * @var string
     */
    public $found_at;

    /**
     * @var int
     */
    public $trip_class;

    /**
     * @var string
     */
    public $number_of_changes;

    /**
     * @var bool
     */
    public $show_to_affiliates;

    /**
     * @var string
     */
    public $gate;

    /**
     * @var int
     */
    public $duration;

    /**
     * @var bool
     */
    public $actual;

}
