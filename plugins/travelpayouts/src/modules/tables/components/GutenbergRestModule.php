<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components;

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
            flights\GutenbergRestCampaign::class,
            hotels\GutenbergRestCampaign::class,
            railway\GutenbergRestCampaign::class,
        ];
    }

    /**
     * @return string
     */
    protected function id()
    {
        return 'tables';
    }

    /**
     * @return string
     */
    protected function title()
    {
        return Travelpayouts::__('Tables');
    }

    /**
     * @return array[]
     */
    protected function getExtraData()
    {
        return [
            'modal' => [
                'title' => Travelpayouts::__('Tables'),
            ],
            'select' => [
                'title' => Travelpayouts::__('Select a table'),
            ],
            'program' => [
                'title' => Travelpayouts::__('Select a program'),
            ],
        ];
    }
}
