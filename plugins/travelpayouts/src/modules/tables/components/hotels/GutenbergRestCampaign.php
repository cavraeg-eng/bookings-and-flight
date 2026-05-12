<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var selectionsDiscount\Table
     */
    public $tp_hotels_selections_discount_shortcodes;
    /**
     * @Inject
     * @var selectionsDate\Table
     */
    public $tp_hotels_selections_date_shortcodes;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return CampaignsSubscriptionsEndpoint::HOTELLOOK_ID;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return Section::isActive();
    }
}
