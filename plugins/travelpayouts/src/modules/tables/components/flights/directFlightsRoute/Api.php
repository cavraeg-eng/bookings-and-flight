<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\directFlightsRoute;

use DateInterval;
use DateTime;
use Exception;
use Travelpayouts\modules\tables\components\api\BaseTokenApiModel;
use Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesDirect\PricesDirectApiModel;
use Travelpayouts\modules\tables\components\flights\directFlights\DirectFlightsResponse;

/**
 * @method DirectFlightsResponse[] getResponseModels()
 */
class Api extends BaseTokenApiModel
{
    protected $responseClass = DirectFlightsResponse::class;
    public $currency;
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
     * Запрашиваем данные из directFlights с различными интервалами
     * Если текущая дата больше 20, то запрашиваем на 4 месяца, если нет, на 3
     * @return array|bool
     * @throws Exception
     */
    protected function request()
    {
        $results = [];
        $modelAttributes = $this->attributes;
        $currentDate = new DateTime();
        // Количество запросов
        $monthCount = (int)$currentDate->format('j') < 20
            ? 3
            : 4;

        for ($i = 1; $i <= $monthCount; $i++) {
            if ($i > 1) {
                // К каждой последующей итерации добавляем 1 месяц
                $currentDate->add(new DateInterval('P1M'));
            }
            $currentDateFormatted = $currentDate->format('Y-m');

            $monthModel = new PricesDirectApiModel([
                'cacheTime' => $this->cacheTime,
            ]);
            $monthModel->scenario = PricesDirectApiModel::SCENARIO_DIRECT_FLIGHTS_ROUTE;
            $monthModel->attributes = array_merge($modelAttributes, [
                'depart_date' => $currentDateFormatted,
                'return_date' => $currentDateFormatted,
            ]);

            if ($monthModel->validate()) {
                if (is_array($response = $monthModel->sendRequest())) {
                    $this->addRequestUrl($monthModel->getDebugData());
                    $results[] = array_values($response);
                }
            }
        }
        return count($results) > 1 ? array_merge(...$results) : $results;
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        throw new Exception('Not implemented');
    }
}
