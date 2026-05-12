<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway;

use Travelpayouts;
use Travelpayouts\components\tables\BaseColumnLabels;

/***
 * Class ColumnLabels
 * @package Travelpayouts\modules\tables\components
 */
class ColumnLabels extends BaseColumnLabels
{
    const TRAIN = 'train';
    const ROUTE = 'route';
    const ROUTE_SHORT = 'route_short';
    const ROUTE_INFO = 'route_info';
    const DEPARTURE = 'departure';
    const ARRIVAL = 'arrival';
    const DURATION = 'duration';
    const PRICES = 'prices';
    const DATES = 'dates';
    const ORIGIN = 'origin';
    const DESTINATION = 'destination';
    const DEPARTURE_TIME = 'departure_time';
    const ARRIVAL_TIME = 'arrival_time';
    const ROUTE_FIRST_STATION = 'route_first_station';
    const ROUTE_LAST_STATION = 'route_last_station';

    /**
     * @inheritDoc
     */
    public function translationKeys()
    {
        return [
            self::TRAIN => 'railway.train',
            self::ROUTE => 'railway.route',
            self::ROUTE_SHORT => 'railway.route_short',
            self::ROUTE_INFO => 'railway.route_info',
            self::DEPARTURE => 'railway.departure',
            self::ARRIVAL => 'railway.arrival',
            self::DURATION => 'railway.duration',
            self::PRICES => 'railway.prices',
            self::DATES => 'railway.dates',
            self::ORIGIN => 'railway.origin',
            self::DESTINATION => 'railway.destination',
            self::DEPARTURE_TIME => 'railway.departure_time',
            self::ARRIVAL_TIME => 'railway.arrival_time',
            self::ROUTE_FIRST_STATION => 'railway.route_first_station',
            self::ROUTE_LAST_STATION => 'railway.route_last_station',
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultTranslations()
    {
        return [
            self::TRAIN => Travelpayouts::__('Train'),
            self::ROUTE => Travelpayouts::__('Route'),
            self::ROUTE_SHORT => Travelpayouts::__('Route short'),
            self::ROUTE_INFO => Travelpayouts::__('Route info'),
            self::DEPARTURE => Travelpayouts::__('Departure'),
            self::ARRIVAL => Travelpayouts::__('Arrival'),
            self::DURATION => Travelpayouts::__('Duration'),
            self::PRICES => Travelpayouts::__('Prices'),
            // dates в Tutu таблице = action column (кнопка), выбор даты из расписания рейсов
            self::DATES => Travelpayouts::__('Button'),
            self::ORIGIN => Travelpayouts::__('From'),
            self::DESTINATION => Travelpayouts::__('To'),
            self::DEPARTURE_TIME => Travelpayouts::__('Departure time'),
            self::ARRIVAL_TIME => Travelpayouts::__('Arrival time'),
            self::ROUTE_FIRST_STATION => Travelpayouts::__('Route`s first station'),
            self::ROUTE_LAST_STATION => Travelpayouts::__('Route`s last station'),
        ];
    }
}
