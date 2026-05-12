<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\links\components;

use Travelpayouts;
use Travelpayouts\components\rest\models\BaseGutenbergRestModule;

class GutenbergRestModule extends BaseGutenbergRestModule
{
    /**
     * @return string[]
     */
    protected function campaignList()
    {
        return [
            Travelpayouts\modules\links\components\flights\GutenbergRestCampaign::class,
            Travelpayouts\modules\links\components\hotels\GutenbergRestCampaign::class,
        ];
    }

    /**
     * @return string
     */
    protected function id()
    {
        return 'links';
    }

    /**
     * @return string
     */
    protected function title()
    {
        return Travelpayouts::__('Links');
    }

    /**
     * @return array[]
     */
    protected function getExtraData()
    {
        return [
            'modal' => [
                'title' => Travelpayouts::__('Links'),
            ],
            'select' => [
                'title' => Travelpayouts::__('Select the link'),
            ],
            'program' => [
                'title' => Travelpayouts::__('Select the campaign'),
            ],
        ];
    }
}
