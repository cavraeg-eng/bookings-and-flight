<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\tutu;

use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest\response\Trip;

class TutuApiResponse extends Trip
{
    /**
     * @var TutuShortcodeModel
     */
    public $shortcodeModel;

    /**
     * @var Trip
     */
    protected $_responseModel;

    /**
     * @param Trip $responseModel
     */
    public function setResponseModel(ApiResponseObject $responseModel): void
    {
        self::configure($this, get_object_vars($responseModel));
        $this->_responseModel = $responseModel;
    }

    public function getMinimalPrice(): ?float
    {
        if (is_array($this->categories)) {
            $priceList = array_column($this->categories, 'price');
            return (float)min($priceList);
        }
        return null;
    }



    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->getMinimalPrice(), 'rub');
            },
        ];
    }
}
