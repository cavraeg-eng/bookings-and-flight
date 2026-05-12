<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\brands;

use Travelpayouts\components\BaseObject;

/**
 * Class BrandSubscriptionService
 * Сервис для проверки подписки на бренды
 */
class BrandSubscriptionService extends BaseObject
{

    /**
     * @var array
     */
    protected static $brandResponseList = [];

    /**
     * Статус подписки для одного бренда
     * @param int $brandId
     * @return bool|null
     */
    public static function getIsSubscribedById(int $brandId): bool
    {
        if (isset(self::$brandResponseList[$brandId])) {
            return self::$brandResponseList[$brandId];
        }

        $endpoint = new BrandSubscriptionEndpoint(['brand_id' => $brandId]);
        $result = $endpoint->sendRequest();

        self::$brandResponseList[$brandId] = $result;

        return $result;
    }

    /**
     * Статус подписки для нескольких брендов
     * @param array $brandIds
     * @return bool[]
     */
    public static function getIsSubscribedByIds(array $brandIds): array
    {
        $results = [];
        $missingIds = [];

        foreach ($brandIds as $brandId) {
            if (isset(self::$brandResponseList[$brandId])) {
                $results[$brandId] = self::$brandResponseList[$brandId];
            } else {
                $missingIds[] = $brandId;
            }
        }

        foreach ($missingIds as $brandId) {
            $results[$brandId] = self::getIsSubscribedById($brandId);
        }

        return $results;
    }

    /**
     * @return bool
     */
    public static function isHotelLookAvailable(): bool
    {
        return false;
    }

    public static function isHotelLookSubscribed(): bool
    {
        return self::getIsSubscribedById(CampaignsSubscriptionsEndpoint::HOTELLOOK_ID);
    }

    /**
     * @return bool
     */
    public static function isAviasalesSubscribed(): bool
    {
        return self::getIsSubscribedById(CampaignsSubscriptionsEndpoint::AVIASALES_ID);
    }

    /**
     * @return bool
     */
    public static function isTutuSubscribed(): bool
    {
        return self::getIsSubscribedById(CampaignsSubscriptionsEndpoint::TP_TUTU_ID);
    }
}