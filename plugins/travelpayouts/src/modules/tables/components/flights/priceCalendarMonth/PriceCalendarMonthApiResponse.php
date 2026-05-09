<?php

namespace Travelpayouts\modules\tables\components\flights\priceCalendarMonth;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix\PriceCalendarMatrixApiResponse;

class PriceCalendarMonthApiResponse extends PriceCalendarMatrixApiResponse
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->value, $this->shortcodeModel->currency);
            },
        ];
    }
}
