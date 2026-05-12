<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\dictionary\Campaigns;
use Travelpayouts\components\widgets\AlertWidget;

class Section extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function __construct(Travelpayouts\modules\widgets\components\Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        $campaign = Campaigns::getInstance()->getItem('100');
        return [
            'title' => $campaign ? $campaign->name : Travelpayouts::__('Flights'),
            'desc' =>  AlertWidget::widget([
                'content' => Travelpayouts::__('These settings are for default settings of widgets, those were embedded  via shortcodes (plugin version before v. 1). The current version of the plugin  embeds all widgets via scripts'),
                'showRoundel' => true,
                'type'=> 'info',
            ]),
            'icon' => 'tp-i-tabler:plane',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'flights';
    }
}
