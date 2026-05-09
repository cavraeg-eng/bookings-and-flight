<?php

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest;


use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class PricesLatestApiModel extends BaseTravelpayoutsApiModel
{
    public const SCENARIO_WITH_REQUIRED_DESTINATION = 'byDestination';

    public const TRIP_CLASS_ECONOMY = 0;
    public const TRIP_CLASS_BUSINESS = 1;
    public const TRIP_CLASS_FIRST = 2;

    /**
     * The airline ticket’s currency.
     * @var string
     */
    public $currency;
    /**
     * The point of departure. The IATA city code or the country code. The length - from 2 to 3 symbols.
     * @var string
     */
    public $origin;

    /**
     * The point of destination. The IATA city code or the country code. The length - from 2 to 3 symbols.
     * @var string
     */
    public $destination;

    /**
     * @var string
     * The beginning of the period, within which the dates of departure fall (in the YYYY-MM-DD format, for example,
     *     2016-05-01). Must be specified if period_type is equal to a month.
     */
    public $beginning_of_period;

    /**
     * @var string
     * The period for which the tickets have been found (the required parameter): year — for the whole time, month —
     *     for a month.
     */
    public $period_type;

    /**
     * @var
     * true - one way, false - back-to-back.
     */
    public $one_way;
    /**
     * @var int
     * The total number of records on a page. The maximum value - 1000.
     */
    public $limit;
    /**
     * @var string
     * The assorting of prices: price — by the price. For the directions, only city - city assorting by the price is
     *     possible; route — by the popularity of a route; distance_unit_price — by the price for 1 km.
     */
    public $sorting = 'price';
    /**
     * @var int
     */
    public $trip_class = self::TRIP_CLASS_ECONOMY;
    /**
     * @var int
     */
    public $trip_duration = 0;
    /**
     * @var bool
     * False - all the prices, true — just the prices, found using the partner marker (recommended)
     */
    public $show_to_affiliates = true;
    /**
     * A page number.
     * @var int
     */
    public $page = 1;

    protected $responseClass = PriceLatestApiResponse::class;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['one_way'], 'boolean'],
            [['currency', 'limit'], 'required'],
            [['limit'], 'number'],
            [['currency', 'origin'], 'string', 'length' => 3],
            [['beginning_of_period', 'page', 'period_type'], 'safe'],
            [['destination'], 'required', 'on' => [self::SCENARIO_WITH_REQUIRED_DESTINATION]],
            [['period_type'], 'in', 'range' => self::availablePeriodTypes()],
            [['sortina'], 'in', 'range' => self::availableSortingTypes()],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v2/prices/latest';
    }

    public static function availablePeriodTypes(): array
    {
        return [
            'year',
            'month',
        ];
    }

    public static function availableSortingTypes(): array
    {
        return [
            'price',
            'city',
            'route',
            'distance_unit_price',
        ];
    }
}
