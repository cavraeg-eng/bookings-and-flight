<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms;

use Travelpayouts;
use Travelpayouts\components\rest\models\BaseGutenbergRestModule;
use Travelpayouts\modules\widgets\components\forms;

class SearchFormRestModule extends BaseGutenbergRestModule
{
    /**
     * @return string[]
     */
    protected function campaignList()
    {
        return [
            forms\flights\GutenbergRestCampaign::class,
            forms\hotels\GutenbergRestCampaign::class,
        ];
    }

    /**
     * @return string
     */
    protected function id()
    {
        return 'widgets';
    }

    /**
     * @return string
     */
    protected function title()
    {
        return Travelpayouts::__('Widgets');
    }

    /**
     * @return array[]
     */
    protected function getExtraData()
    {
        return [
            'modal' => [
                'title' => Travelpayouts::__('Widgets'),
            ],
            'select' => [
                'title' => Travelpayouts::__('Select the widget'),
            ],
            'program' => [
                'title' => Travelpayouts::__('Select the campaign'),
            ],
        ];
    }
}
