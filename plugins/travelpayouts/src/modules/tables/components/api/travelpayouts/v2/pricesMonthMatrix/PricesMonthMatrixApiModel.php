<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class PricesMonthMatrixApiModel extends BaseTravelpayoutsApiModel
{
    protected $responseClass = PriceCalendarMatrixApiResponse::class;
    /**
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
     * The beginning of the month in the YYYY-MM-DD format.
     */
    public $month;
    /**
     * @var bool
     * False - all the prices, true - just the prices, found using the partner marker (recommended).
     */
    public $show_to_affiliates;

    /**
     * @var bool
     * true - one way, false - back-to-back.
     */
    public $one_way = false;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin', 'destination'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
            // Allowed only Y-m-d format
            [['month'], 'match', 'pattern' => '/^(\d{4})-(\d{2})-(\d{2})$/'],
            [['one_way'], 'boolean'],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'https://api.travelpayouts.com/v2/prices/month-matrix';
    }

}
