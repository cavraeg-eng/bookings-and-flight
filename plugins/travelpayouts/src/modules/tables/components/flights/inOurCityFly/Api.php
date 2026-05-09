<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\inOurCityFly;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\priceLatest\PricesLatestApiModel;

class Api extends PricesLatestApiModel
{
    public $destination;

    public function rules()
    {
        return array_merge(BaseTravelpayoutsApiModel::rules(), [
            [['one_way'], 'string'],
            [['currency', 'destination', 'limit'], 'required'],
            [['limit'], 'number'],
            [['currency', 'destination'], 'string', 'length' => 3],
            [['beginning_of_period', 'page', 'period_type'], 'safe'],
        ]);
    }
}
