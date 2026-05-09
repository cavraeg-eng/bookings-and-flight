<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels;

use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;

abstract class HotelLookWidgetShortcodeModel extends BaseWidgetShortcodeModel
{
    public static function isActive(): bool
    {
        return BrandSubscriptionService::isHotelLookAvailable();
    }

}