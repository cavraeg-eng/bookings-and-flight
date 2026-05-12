<?php

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;

class SettingsSection extends ModuleSection
{
    public function __construct(Travelpayouts\modules\tables\components\Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Settings'),
            'icon' => 'tp-i-tabler:table-options',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'settings';
    }
}
