<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\moneyScript\widgets;

use Travelpayouts\Components\BaseWidget;
use Travelpayouts\components\LanguageHelper;

class MoneyScriptSectionDescriptionWidget extends BaseWidget
{
    public function getSupportUrl(): string
    {
        return LanguageHelper::isRuDashboard() ?
            'https://support.travelpayouts.com/hc/ru/articles/360012913480' :
            'https://support.travelpayouts.com/hc/en-us/articles/360012913480-Automatic-replacement-of-links-on-the-website';;

    }

    public function run(): string
    {
        return $this->render('section_description');
    }
}