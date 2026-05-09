<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\hotelLook\locationMap\response;

use Travelpayouts\components\api\ApiResponseObject;

class HotelLastPriceInfo extends ApiResponseObject
{

    /**
     * @var float
     */
    public $price;
    /**
     * @var float
     */
    public $old_price;
    /**
     * @var integer
     */
    public $discount;
    /**
     * @var integer
     */
    public $insertion_time;
    /**
     * @var integer
     */
    public $nights;
    /**
     * @var HotelSearchParams|null
     */
    public $search_params;
    /**
     * @var float
     */
    public $price_pn;
    /**
     * @var float
     */
    public $old_price_pn;
}
