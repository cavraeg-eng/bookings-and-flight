<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\airlineDirections;

use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class AirlineDirectionsApiModel extends BaseTravelpayoutsApiModel
{
    protected $responseClass = AirlineDirectionsApiResponse::class;

    /**
     * @var string|int
     * Records limit per page. Default value is 100. Not less than 1000.
     */
    public $limit = 100;
    /**
     * @var string
     * IATA code of airline.
     */
    public $airline_code;

    /**
     * @return array|array[]
     * @see $airline_code
     * @see $limit
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['limit', 'airline_code'], 'required'],
            [['limit'], 'number']
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v1/airline-directions';
    }

    public function afterRequest()
    {
        parent::afterRequest();
        $response = $this->response;
        if (ArrayHelper::isAssociative($response)) {
            $result = [];
            $index = 0;
            foreach ($response as $key => $value) {
                $result[] = ['key' => $key, 'value' => $value, 'index' => $index++];
            }
            $this->response = $result;
        } else {
            $this->response = [];
        }

    }
}
