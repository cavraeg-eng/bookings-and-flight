<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var \Travelpayouts\modules\searchForms\components\SearchFormShortcode
     */
    public $tp_search_shortcodes;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return CampaignsSubscriptionsEndpoint::AVIASALES_ID;
    }
}
