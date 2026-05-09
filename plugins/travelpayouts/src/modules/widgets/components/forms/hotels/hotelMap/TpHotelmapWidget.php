<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotelMap;

use Travelpayouts;
use Travelpayouts\modules\widgets\components\forms\hotels\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpHotelmapWidget extends Fields
{
    /**
     * @var array
     */
    public $map_dimensions;
    /**
     * @var string
     */
    public $color_pallete;
    /**
     * @var string
     */
    public $pins_color;
    /**
     * @var string
     */
    public $texts_color;
    /**
     * @var string
     */
    public $allow_dragging;
    /**
     * @var string
     */
    public $enable_zooming;
    /**
     * @var string
     */
    public $zoom;
    /**
     * @var string
     */
    public $zooming_during_scrolling;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            LegacyReduxFields::widget_preview(
                $this->optionPath,
                LegacyReduxFields::WIDGET_PREVIEW_TYPE_IFRAME,
                '//maps.avs.io/hotels?color={{fields.color_pallete || "#00AFE4"}}&locale={locale}&marker=132474.hotelsmap&changeflag=0&draggable={{toBoolean(fields.allow_dragging)}}&map_styled=false&map_color=#00b1dd&contrast_color=#FFFFFF&disable_zoom={{not(fields.enable_zooming,"1")}}&base_diameter=16&scrollwheel=false&host=hotellook.com&lat=52.5234&lng=13.4114&zoom=12',
                [
                    'width' => '100%',
                    'height' => '300px',
                ]
            ),


            LegacyReduxFields::dimensions(
                'map_dimensions',
                Travelpayouts::__('Map dimensions'),
                500,
                300
            ),
            LegacyReduxFields::color_scheme(
                'color_pallete',
                Travelpayouts::__('Color scheme'),
                Travelpayouts::__('Select last pallet to set custom values'),
                [
                    '#98056A' => [
                        '#98056A',
                    ],
                    '#00AFE4' => [
                        '#00AFE4',
                    ],
                    '#74BA00' => [
                        '#74BA00',
                    ],
                    '#DB5521' => [
                        '#DB5521',
                    ],
                    '#FFBC00' => [
                        '#FFBC00',
                    ],
                    'custom' => [
                        '#A2CCFF',
                    ],
                ],
                '#FFBC00'
            ),
            LegacyReduxFields::color(
                'pins_color',
                Travelpayouts::__('Pin color'),
                null,
                '#A2CCFF',
                [
                    LegacyReduxFields::get_ID($this->optionPath, 'color_pallete'),
                    '=',
                    'custom',
                ]
            ),
            LegacyReduxFields::color(
                'texts_color',
                Travelpayouts::__('Text color'),
                null,
                '#FFFFFF',
                [
                    LegacyReduxFields::get_ID($this->optionPath, 'color_pallete'),
                    '=',
                    'custom',
                ]
            ),
            LegacyReduxFields::checkbox('allow_dragging', Travelpayouts::__('Allow dragging')),
            LegacyReduxFields::checkbox('enable_zooming', Travelpayouts::__('Enable zooming')),
            LegacyReduxFields::simple_text_slider(
                'zoom',
                Travelpayouts::__('Zoom'),
                12,
                1,
                19,
                [
                    LegacyReduxFields::get_ID(
                        'widgets_hotels_' . $this->id,
                        'enable_zooming'
                    ),
                    '=',
                    true
                ]
            ),
            LegacyReduxFields::checkbox(
                'zooming_during_scrolling',
                Travelpayouts::__('Zooming during scrolling')
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_hotelmap_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Hotels map');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Map showing available hotels in the selected location and their approximate prices.');
    }
}
