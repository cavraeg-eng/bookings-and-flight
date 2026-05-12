<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\subscriptions;

use Travelpayouts;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpSubscriptionsWidget extends Fields
{
    /**
     * @var string
     */
    public $scalling_width_toggle;
    /**
     * @var string
     */
    public $scalling_width;
    /**
     * @var string
     */
    public $city_departure;
    /**
     * @var string
     */
    public $city_arrive;
    /**
     * @var string
     */
    public $bg_pallet;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $bgPalletFieldId = $this->optionPath . '_bg_pallet';

        return array_merge(
            [

                LegacyReduxFields::widget_preview(
                    $this->optionPath,
                    LegacyReduxFields::WIDGET_PREVIEW_TYPE_SCRIPT,
                    '//www.travelpayouts.com/subscription_widget/widget.js?backgroundColor={{fields.bg_pallet || "#2300b1"}}&marker=132474&host=hydra.aviasales.ru&originIata=MOW&originName=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0&destinationIata=BKK&destinationName=%D0%91%D0%B0%D0%BD%D0%B3%D0%BA%D0%BE%D0%BA&powered_by=true'
                ),
            ],
            LegacyReduxFields::width_toggle(
                $this->id,
                514,
                LegacyReduxFields::get_ID(
                    $this->optionPath,
                    'scalling_width_toggle'
                )
            ),
            LegacyReduxFields::flight_directions(),
            [
                [
                    'id' => 'pallets',
                    'type' => 'image_select',
                    'presets' => true,
                    'title' => Travelpayouts::__('Color scheme'),
                    'options' => [
                        '1' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_1.png'),
                            'presets' => [
                                $bgPalletFieldId => '#222222',
                            ],
                        ],
                        '2' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_2.png'),
                            'presets' => [
                                $bgPalletFieldId => '#98056A',
                            ],
                        ],
                        '3' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_3.png'),
                            'presets' => [
                                $bgPalletFieldId => '#00AFE4',
                            ],
                        ],
                        '4' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_4.png'),
                            'presets' => [
                                $bgPalletFieldId => '#74BA00',
                            ],
                        ],
                        '5' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_5.png'),
                            'presets' => [
                                $bgPalletFieldId => '#DB5521',
                            ],
                        ],
                        '6' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_6.png'),
                            'presets' => [
                                $bgPalletFieldId => '#FFBC00',
                            ],
                        ],
                        '7' => [
                            'alt' => 'Preset 1',
                            'img' => LegacyReduxFields::get_image_url('admin/widgets/flights/tp_subscriptions_widget/pallet_7.png'),
                            'presets' => [
                                $bgPalletFieldId => '#DADADA',
                            ],
                        ],
                    ],
                ],
                LegacyReduxFields::color(
                    'bg_pallet',
                    Travelpayouts::__('Color scheme custom'),
                    '',
                    '#2300b1'
                ),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_subscriptions_widget';
    }

    /**
     * @inheritDoc
     */
    public static function isActive(): bool
    {
        return LanguageHelper::tableTranslatorLocale(false) === Translator::RUSSIAN;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Subscribe to price changes.');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Notifications about flights cost changes by direction based on a selected date/month');
    }
}
