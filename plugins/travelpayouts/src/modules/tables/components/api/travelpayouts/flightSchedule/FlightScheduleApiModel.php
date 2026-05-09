<?php

namespace Travelpayouts\modules\tables\components\api\travelpayouts\flightSchedule;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class FlightScheduleApiModel extends BaseTravelpayoutsApiModel
{
    public $origin;
    public $destination;
    public $service;
    public $locale;
    public $non_direct_flights;
    public $airline;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], 'string', 'length' => 3],
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'https://suggest.travelpayouts.com/api_flight_schedule';
    }

    public function afterRequest()
    {
        $response = $this->response;

        if (is_array($response) && isset($response['result'])) {
            $this->response = [
                'success' => true,
                'data' => $response['result']
            ];
        }

        parent::afterRequest();
    }
}
