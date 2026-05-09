<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesCheap;

use DateTime;
use Travelpayouts\components\InjectedModel;

class PricesCheapApiResponse extends InjectedModel
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
     * @var int
     */
    public $number_of_changes;


    public function getDepartureDate(): ?DateTime
    {
        return $this->departure_at
            ? $this->parseDate($this->departure_at)
            : null;
    }

    public function getReturnDate(): ?DateTime
    {
        return $this->return_at
            ? $this->parseDate($this->return_at)
            : null;
    }

    public function getExpiresAtDate(): ?DateTime
    {
        return $this->expires_at
            ? $this->parseDate($this->expires_at)
            : null;
    }

    protected function parseDate(string $value): ?DateTime
    {
        return DateTime::createFromFormat('Y-m-d\TH:i:sT', $value);
    }
}
