<?php

namespace Travelpayouts\modules\linkForms;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\admin\redux\extensions\linksForms\LinksForms;

class LinksForm extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Link substitution'),
            'icon' => 'el el-link',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            [
                'id' => 'shortcodes',
                'type' => LinksForms::TYPE,
                'title' => Travelpayouts::__('Here you can add referral links that need to be substituted for the given anchor 
                phrases. Anchor is case sensitive.'),
                'desc' => Travelpayouts::__('In this section, you can add shortcodes for search forms 
                configured in the admin panel of your Travelpayouts account (https://www.travelpayouts.com/tools/forms). 
                Detailed instructions are available here (https://travel-template.dist.ooo/search-form.html)'),
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'add_links';
    }
}
