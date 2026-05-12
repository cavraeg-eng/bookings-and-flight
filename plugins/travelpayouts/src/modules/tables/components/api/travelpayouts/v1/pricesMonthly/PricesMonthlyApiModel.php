<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesMonthly;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class PricesMonthlyApiModel extends BaseTravelpayoutsApiModel
{
    protected $responseClass = PricesMonthlyApiResponse::class;
    public $currency = 'RUB';
    public $origin;
    public $destination;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin', 'destination'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v1/prices/monthly';
    }

}
