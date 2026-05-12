<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesDirect;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class PricesDirectApiModel extends BaseTravelpayoutsApiModel
{
    protected $responseClass = PricesDirectResponse::class;

    const SCENARIO_DIRECT_FLIGHTS_ROUTE = 'directFlightsRoute';

    public $currency = 'RUB';
    public $origin;
    public $destination;
    /**
     * @var string
     * Day or month of departure (yyyy-mm-dd or yyyy-mm).
     */
    public $depart_date;
    /**
     * @var string
     * Day or month of return (yyyy-mm-dd or yyyy-mm).
     */
    public $return_date;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
            [['depart_date', 'return_date'], 'safe', 'on' => [self::SCENARIO_DIRECT_FLIGHTS_ROUTE]],
        ]);
    }

    public function afterRequest()
    {
        parent::afterRequest();
        $response = $this->response;
        if (is_array($response)) {
            $this->response = array_reduce(array_keys($response), $this->mapResponse($response), []);
        }
    }

    /**
     * Переводим выдачу вида
     *  ['AAQ' =>[
     *      flight1,
     *      flight2,
     *  ]]
     *  к виду
     *  [flight1,flight2]
     * @param $response
     * @return \Closure
     */
    protected function mapResponse($response)
    {
        $origin = $this->origin;
        return static function ($accumulator, $destination) use ($response, $origin) {
            if (is_array($response[$destination])) {
                $flightsList = array_map(static function ($flight) use ($destination, $origin) {
                    return array_merge($flight, [
                        'destination' => $destination,
                        'origin' => $origin,
                    ]);
                }, $response[$destination]);
                return array_merge($accumulator, $flightsList);
            }
            return $accumulator;
        };
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v1/prices/direct';
    }
}
