<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\cityDirections;

use Travelpayouts\components\api\ApiResponseObject;

class CityDirectionApiResponse extends ApiResponseObject
{
    /**
     * @var string
     */
    public $origin;
    /**
     * @var string
     */
    public $destination;
    /**
     * @var float
     */
    public $price;
    /**
     * @var string
     */
    public $airline;
    /**
     * @var int
     */
    public $flight_number;
    /**
     * @var string
     */
    public $departure_at;
    /**
     * @var string
     */
    public $return_at;
    /**
     * @var int
     */
    public $transfers;
    /**
     * @var string
     */
    public $expires_at;
}
