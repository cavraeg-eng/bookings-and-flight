<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\selectionsDiscount;

use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\hotelLook\locationMap\response\Hotel;
use Travelpayouts\modules\tables\components\hotels\components\HotelButtonModel;

/**
 * @property Hotel $responseModel
 */
class SelectionsDiscountApiResponse extends Hotel
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    /**
     * @var Hotel
     */
    protected $_responseModel;

    /**
     * @return Hotel
     */
    public function getResponseModel()
    {
        return $this->_responseModel;
    }

    /**
     * @param Hotel $responseModel
     */
    public function setResponseModel(ApiResponseObject $responseModel): void
    {
        self::configure($this, get_object_vars($responseModel));

        $this->_responseModel = $responseModel;
    }

    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->getPrice(), $this->shortcodeModel->currency);
            },
        ];
    }

    public function getButtonModel(): HotelButtonModel
    {
        $model = new HotelButtonModel();
        $model->hotelId = $this->hotel_id;
        $model->checkInDate = $this->last_price_info->search_params->checkIn ?? null;
        $model->checkOutDate = $this->last_price_info->search_params->checkOut ?? null;
        return $model;
    }

}
