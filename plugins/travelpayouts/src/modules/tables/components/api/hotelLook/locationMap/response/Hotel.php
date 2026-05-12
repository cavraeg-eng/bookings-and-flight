<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\hotelLook\locationMap\response;

class Hotel extends \Travelpayouts\components\api\ApiResponseObject
{

    /**
     * @var int
     */
    public $hotel_id;
    /**
     * @var float
     */
    public $distance;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $stars;
    /**
     * @var int
     */
    public $rating;
    /**
     * @var string
     */
    public $ty_summary;
    /**
     * @var string
     */
    public $property_type;
    /**
     * @var string[]
     */
    public $hotel_type;
    /**
     * @var HotelLastPriceInfo|null
     */
    public $last_price_info;
    /**
     * @var boolean
     */
    public $has_wifi;

    public function getPricePerNight(): ?float
    {
        return $this->last_price_info->price_pn ?? null;
    }

    public function getOldPricePerNight(): ?float
    {
        return $this->last_price_info->old_price_pn ?? null;
    }

    public function getDiscount(): ?int
    {
        return $this->last_price_info->discount ?? null;
    }

    public function getPrice(): ?float
    {
        return $this->last_price_info->price ?? null;

    }

    public function getOldPrice(): ?float
    {
        return $this->last_price_info->old_price ?? null;

    }
}
