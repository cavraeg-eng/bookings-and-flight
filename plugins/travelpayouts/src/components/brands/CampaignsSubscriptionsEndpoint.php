<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\brands;

use Exception;
use Travelpayouts;
use Travelpayouts\components\api\ApiEndpoint;
use Travelpayouts\components\api\ApiResponseObject;

/**
 * Class Subscriptions
 * @package Travelpayouts\components
 */
class CampaignsSubscriptionsEndpoint extends ApiEndpoint
{
    const TP_TUTU_ID = 45;
    const HOTELLOOK_ID = 101;
    const AVIASALES_ID = 100;

    public function getResponse(): ?Travelpayouts\components\httpClient\CachedResponse
    {
        return $this->getClient()->get('https://www.travelpayouts.com/campaigns/get_campaigns_subscriptions', [
            'params' => [
                'marker' => $this->getMarker(),
            ],
        ]);
    }

    protected function clientOptions(): array
    {
        return [
            'timeout' => 15,
        ];
    }

    /**
     * @return SubscriptionsResponse|null
     */
    protected function getData(): ?SubscriptionsResponse
    {
        $response = $this->getResponse();
        return $response && $response->getIsSuccess()
            ? SubscriptionsResponse::createFromArray($response->getJSON())
            : null;
    }

    /**
     * Проверяем доступность сервиса
     * @param $id
     * @return bool
     */
    public function isActive($id): bool
    {
        $data = $this->getData();
        return $data && $data->getIsActiveById($id);
    }

    /**
     * Получаем маркер напрямую, минуя travelPayouts DI
     * @return string|null
     */
    protected function getMarker()
    {
        try {
            $redux = Travelpayouts::getInstance()->redux;
            $options = $redux->get_options();
            return isset($options['account_api_marker']) && !empty($options['account_api_marker'])
                ? $options['account_api_marker']
                : null;
        } catch (Exception $e) {
            return null;
        }
    }
}

class SubscriptionsResponse extends ApiResponseObject
{
    /**
     * @var int[]
     */
    public $subscriptions = [];

    public function getIsActiveById($id): bool
    {
        return (is_int($id) || is_string($id)) && in_array((int)$id, $this->subscriptions, true);
    }
}
