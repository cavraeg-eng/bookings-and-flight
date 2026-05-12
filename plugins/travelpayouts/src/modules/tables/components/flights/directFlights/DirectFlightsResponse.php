<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\directFlights;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesDirect\PricesDirectResponse;

class DirectFlightsResponse extends PricesDirectResponse
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    public function getFullFlightNumber(): string
    {
        return $this->airline . ' ' . $this->flight_number;
    }

    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->price, $this->shortcodeModel->currency);
            },
        ];
    }

}
