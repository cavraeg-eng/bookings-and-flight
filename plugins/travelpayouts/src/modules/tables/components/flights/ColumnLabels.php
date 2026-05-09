<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;

use Travelpayouts;
use Travelpayouts\components\tables\BaseColumnLabels;

/***
 * Class ColumnLabels
 * @package Travelpayouts\modules\tables\components
 */
class ColumnLabels extends BaseColumnLabels
{
    const DEPARTURE_AT = 'departure_at';
    const NUMBER_OF_CHANGES = 'number_of_changes';
    const BUTTON = 'button';
    const PRICE = 'price';
    const TRIP_CLASS = 'trip_class';
    const DISTANCE = 'distance';
    const RETURN_AT = 'return_at';
    const AIRLINE_LOGO = 'airline_logo';
    const FLIGHT_NUMBER = 'flight_number';
    const FLIGHT = 'flight';
    const AIRLINE = 'airline';
    const DESTINATION = 'destination';
    const ORIGIN_DESTINATION = 'origin_destination';
    const PLACE = 'place';
    const DIRECTION = 'direction';
    const ORIGIN = 'origin';
    const FOUND_AT = 'found_at';
    const PRICE_DISTANCE = 'price_distance';
    const TIME_AND_STOPS = 'time_and_stops';
    const ROUTE = 'route';
    const SCHEDULE = 'schedule';
    const FULL_AIRLINE_LOGO = 'full_airline_logo';
    const COMPACT_AIRLINE_LOGO = 'compact_airline_logo';
    const AIRLINE_NAME = 'airline_name_string';

    public function translationKeys()
    {
        return [
            self::DEPARTURE_AT => 'flights.departure_at',
            self::NUMBER_OF_CHANGES => 'flights.number_of_changes',
            self::BUTTON => 'flights.button_column_title',
            self::PRICE => 'flights.price',
            self::TRIP_CLASS => 'flights.trip_class',
            self::DISTANCE => 'flights.distance',
            self::RETURN_AT => 'flights.return_at',
            self::AIRLINE_LOGO => 'flights.airline_logo',
            self::FLIGHT_NUMBER => 'flights.flight_number',
            self::FLIGHT => 'flights.flight',
            self::AIRLINE => 'flights.airline',
            self::DESTINATION => 'flights.destination',
            self::ORIGIN_DESTINATION => 'flights.origin_destination',
            self::PLACE => 'flights.place',
            self::DIRECTION => 'flights.direction',
            self::ORIGIN => 'flights.origin',
            self::FOUND_AT => 'flights.found_at',
            self::PRICE_DISTANCE => 'flights.price_distance',
            self::TIME_AND_STOPS => 'flights.time_and_stops',
            self::ROUTE => 'flights.route',
            self::SCHEDULE => 'flights.schedule',
            self::COMPACT_AIRLINE_LOGO => 'flights.airline',
            self::FULL_AIRLINE_LOGO => 'flights.airline',
            self::AIRLINE_NAME => 'flights.airline',
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultTranslations()
    {
        return [
            self::DEPARTURE_AT => Travelpayouts::__('Departure date'),
            self::NUMBER_OF_CHANGES => Travelpayouts::__('Stops'),
            self::BUTTON => Travelpayouts::__('Button'),
            self::PRICE => Travelpayouts::__('Price'),
            self::TRIP_CLASS => Travelpayouts::__('Flight class'),
            self::DISTANCE => Travelpayouts::__('Distance'),
            self::RETURN_AT => Travelpayouts::__('Return date'),
            self::AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::FLIGHT_NUMBER => Travelpayouts::__('Flight number'),
            self::FLIGHT => Travelpayouts::__('Flight'),
            self::AIRLINE => Travelpayouts::__('Airline name'),
            self::AIRLINE_NAME => Travelpayouts::__('Airline name'),
            self::FULL_AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::COMPACT_AIRLINE_LOGO => Travelpayouts::__('Compact airline logo with name'),
            self::DESTINATION => Travelpayouts::__('Destination'),
            self::ORIGIN_DESTINATION => Travelpayouts::__('Origin - Destination'),
            self::PLACE => Travelpayouts::__('Rank'),
            self::DIRECTION => Travelpayouts::__('Direction'),
            self::ORIGIN => Travelpayouts::__('Origin'),
            self::FOUND_AT => Travelpayouts::__('When found'),
            self::PRICE_DISTANCE => Travelpayouts::__('Price/distance'),
            self::TIME_AND_STOPS => Travelpayouts::__('Time and stops'),
            self::ROUTE => Travelpayouts::__('Route'),
            self::SCHEDULE => Travelpayouts::__('Schedule'),
        ];
    }

    public function getDashboardColumnLabels(array $names = null): array
    {
        $labelsList = array_merge(parent::getDashboardColumnLabels($names), [
            self::AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::FULL_AIRLINE_LOGO => Travelpayouts::__('Full airline logo'),
            self::COMPACT_AIRLINE_LOGO => Travelpayouts::__('Compact airline logo with name'),
        ]);

        if (is_array($names)) {
            $result = [];
            foreach ($names as $key) {
                $result[$key] = array_key_exists($key, $labelsList) ? $labelsList[$key] : null;
            }
            return $result;
        }

        return $labelsList;
    }

}
