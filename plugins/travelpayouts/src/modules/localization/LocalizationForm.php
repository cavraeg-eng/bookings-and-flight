<?php

namespace Travelpayouts\modules\localization;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;

/**
 * Class SettingsForm
 * @package Travelpayouts\src\modules\settings
 */
class LocalizationForm extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Localization'),
            'icon' => 'el el-cog',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'localization';
    }
}
