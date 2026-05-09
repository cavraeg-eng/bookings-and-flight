<?php

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts;
use Travelpayouts\components\brands\BrandSubscriptionService;

/**
 * Class HotelSettingsSection
 * @package Travelpayouts\modules\tables\components\settings
 */
class HotelSettingsSection extends Fields
{
    /**
     * @var string
     */
    public $use_booking_com = '0';
    /**
     * @var string
     */
    public $theme = 'default-theme';

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'use_booking_com' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Redirect to Booking.com'))
                ->setDefault((bool)$this->use_booking_com),
            'theme' => (new ThemeSelectField())
                ->setOptions($this->themeList()),
        ];
    }

    /**
     * @return array
     */
    public function themeList(): array
    {
        return [
            'default-theme' => Travelpayouts::__('Default theme'),
            'red-button-table' => Travelpayouts::__('Bright theme with a red button'),
            'blue-table' => Travelpayouts::__('Light theme with a blue button'),
            'grey-salad-table' => Travelpayouts::__('Light theme with a light green button'),
            'purple-table' => Travelpayouts::__('Light theme with a purple button'),
            'black-and-yellow-table' => Travelpayouts::__('Dark theme with a yellow button'),
            'dark-and-rainbow' => Travelpayouts::__('Dark theme with a coral button'),
            'light-and-plum-table' => Travelpayouts::__('Light theme with a plum search column'),
            'light-yellow-and-darkgray' => Travelpayouts::__('Light theme with a dark search column'),
            'mint-table' => Travelpayouts::__('Light theme with a mint button'),
            CustomTableStylesSection::CUSTOM_THEME => Travelpayouts::__('Custom theme styles'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'hotels';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Customize hotels tables settings');
    }

    /**
     * @inheritDoc
     */
    public static function isActive(): bool
    {
        return BrandSubscriptionService::isHotelLookAvailable();
    }
}
