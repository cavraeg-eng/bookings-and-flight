<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\help;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\section\fields\Raw;

class HelpSection extends ModuleSection
{

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Help'),
            'icon' => 'tp-i-tabler:lifebuoy',
        ];
    }

    public function fields(): array
    {
        return [
            'helpSection' => (new Raw())->setContent(HtmlHelper::tagArrayContent('div', [
				'style'=> 'margin: -15px -10px;'
			],   HtmlHelper::reactWidget('TravelpayoutsZendeskFeed', [
				'lang' => LanguageHelper::isRuDashboard()
					? 'ru'
					: 'en',
			]))),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'help';
    }
}
