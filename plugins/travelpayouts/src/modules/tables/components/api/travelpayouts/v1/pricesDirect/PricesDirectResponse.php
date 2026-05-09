<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesDirect;

use Travelpayouts\components\api\ApiResponseObject;

class PricesDirectResponse extends ApiResponseObject
{
    /**
     * @var int
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
     * @var string
     */
    public $expires_at;
    /**
     * @var string
     */
    public $destination;
    /**
     * @var string
     */
    public $origin;
}
