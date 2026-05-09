<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\links\components\hotels;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var Shortcode
     */
    public $tp_link;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return CampaignsSubscriptionsEndpoint::HOTELLOOK_ID;
    }

    public function isActive(): bool
    {
        return BrandSubscriptionService::isHotelLookAvailable();
    }
}
