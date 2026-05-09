<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components;

use Travelpayouts;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\LanguageHelper;

class HeadWayWidget extends BaseObject
{
    const ACCOUNT_ID_RU = 'Jrn8zJ';
    const ACCOUNT_ID_EN = 'JVpvjx';

    public static function render()
    {
        return HtmlHelper::reactWidget('TravelpayoutsHeadwayButton', [
            'account' => LanguageHelper::dashboardLocale() === LanguageHelper::DASHBOARD_RUSSIAN ? self::ACCOUNT_ID_RU : self::ACCOUNT_ID_EN,
            'buttonTitle' => Travelpayouts::_x('Changelog', 'headway.buttonTitle'),
            'translations' => [
                'title' => Travelpayouts::_x('Latest changes', 'headway.translations.titleΩ'),
                'readMore' => Travelpayouts::_x('Read more', 'headway.translations.readMoreΩ'),
                'labels' => [
                    'new' => Travelpayouts::_x('New', 'headway.translations.labels.new'),
                    'improvement' => Travelpayouts::_x('Updates', 'headway.translations.labels.improvement'),
                    'fix' => Travelpayouts::_x('Fixes', 'headway.translations.labels.fix'),
                ],
                'footer' => Travelpayouts::_x('Read more', 'headway.translations.footer'),
            ],
            'widgetPosition' => 'top-right',
        ]);

    }
}
