<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\hotelLook\locationMap\response;

use Travelpayouts\components\api\ApiResponseObject;

class HotelSearchParams extends ApiResponseObject
{
    /**
     * @var integer
     */
    public $adults;
    /**
     * @var array
     */
    public $children;
    /**
     * @var string
     */
    public $checkIn;
    /**
     * @var string
     */
    public $checkOut;
}
