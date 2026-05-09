<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule\response;

use Travelpayouts;
use Travelpayouts\components\api\ApiResponseObject;

class Title extends ApiResponseObject
{

    /**
     * @var bool
     */
    public $flights_every_day;
    /**
     * @var int
     */
    public $flights_number;
    /**
     * @var MinFlightDuration
     */
    public $min_flight_duration;

    public function flightDurationText($locale): string
    {
        $minFlightDuration = $this->min_flight_duration;
        $days = $minFlightDuration->days;
        $hours = $minFlightDuration->hours;
        $min = $minFlightDuration->min;

        return implode(' ', array_filter([
            $days
                ? $days . $this->translate('flights.title.duration_short_days', $locale)
                : null,
            $hours
                ? $hours . $this->translate('flights.title.duration_short_hours', $locale)
                : null,
            $min
                ? $min . $this->translate('flights.title.duration_short_min', $locale)
                : null,
        ]));
    }

    protected function translate($key, $locale): string
    {
        return Travelpayouts::t(
            $key, [], 'tables', $locale
        );
    }

}
