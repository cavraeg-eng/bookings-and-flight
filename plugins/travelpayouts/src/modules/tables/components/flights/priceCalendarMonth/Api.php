<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\priceCalendarMonth;

use Exception;
use Travelpayouts\modules\tables\components\api\BaseTokenApiModel;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix\PricesMonthMatrixApiModel;

class Api extends BaseTokenApiModel
{
    protected $responseClass = PriceCalendarMonthApiResponse::class;
    public $currency;
    public $origin;
    public $destination;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin', 'destination'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
        ]);
    }

    protected function request()
    {
        $apiOptions = [
            'cacheTime' => $this->cacheTime,
            'one_way' => true
        ];

        $results = [];
        $currentMonthModel = new PricesMonthMatrixApiModel($apiOptions);

        $currentMonthModel->attributes = array_merge($this->attributes, [
            'month' => date('Y-m-01'),
        ]);
        $currentMonthResult = $currentMonthModel->sendRequest();
        $this->addRequestUrl($currentMonthModel->getDebugData());

        if (is_array($currentMonthResult) && !empty($currentMonthResult)) {
            $results = array_merge($results, $currentMonthResult);
        }

        if ($this->isNeedToFetchNextMonth()) {
            $nextMonthModel = new PricesMonthMatrixApiModel($apiOptions);
            $nextMonthModel->attributes = array_merge($this->attributes, [
                'month' => date('Y-m-d', strtotime('first day of next month')),
            ]);
            $nextMonthResult = $nextMonthModel->sendRequest();
            $this->addRequestUrl($nextMonthModel->getDebugData());
            // Склеиваем результаты с предыдущим периодом
            if (is_array($nextMonthResult) && !empty($nextMonthResult)) {
                $results = array_merge($results, $nextMonthResult);
            }
        }

        return $results;
    }

    /**
     * Прошло больше половины месяца?
     * @return bool
     */
    protected function isNeedToFetchNextMonth(): bool
    {
        $currentDay = date('d');
        return $currentDay > ceil(date('t', strtotime('last day of this month')) / 2);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function endpointUrl(): string
    {
        throw new Exception('Not implemented');
    }
}
