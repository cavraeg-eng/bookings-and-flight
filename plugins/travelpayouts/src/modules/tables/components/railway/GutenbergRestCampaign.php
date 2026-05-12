<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var tutu\TutuShortcodeModel
     */
    public $tp_tutu_shortcodes;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return CampaignsSubscriptionsEndpoint::TP_TUTU_ID;
    }

    public function isActive(): bool
    {
        return Section::isActive();
    }

}
