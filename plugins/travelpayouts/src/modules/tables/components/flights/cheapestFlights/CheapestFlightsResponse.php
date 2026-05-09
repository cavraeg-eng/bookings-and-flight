<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\cheapestFlights;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesCheap\PricesCheapApiResponse;

class CheapestFlightsResponse extends PricesCheapApiResponse
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    /**
     * @return string
     */
    public function getFlightNumber(): ?string
    {
        return $this->airline . ' ' . $this->flight_number;
    }

    /**
     * @return string
     */
    public function getFlight(): ?string
    {
        return $this->flight_number;
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
