<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\map;

use Travelpayouts;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

/**
 * Class TpMapWidget
 * @package Travelpayouts\modules\widgets\components\forms\flights\map
 */
class TpMapWidget extends Fields
{
    /**
     * @var array
     */
    public $map_dimensions;
    /**
     * @var string
     */
    public $only_direct_flight;
    /**
     * @var string
     */
    public $show_logo;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            LegacyReduxFields::widget_preview(
                $this->optionPath,
                LegacyReduxFields::WIDGET_PREVIEW_TYPE_IFRAME,
                '//maps.avs.io/flights/?auto_fit_map=true&hide_sidebar=true&hide_reformal=true&disable_googlemaps_ui=true&zoom=3&show_filters_icon=true&redirect_on_click=true&small_spinner=true&hide_logo={{not(fields.show_logo, 0)}}&direct={{fields.only_direct_flight || 0}}&lines_type=TpLines&cluster_manager=TpWidgetClusterManager&marker=132474.map&show_tutorial=false&locale={locale}&host=map.aviasales.ru&origin_iata=MOW',
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
            LegacyReduxFields::checkbox(
                'only_direct_flight',
                Travelpayouts::__('Show direct flights only')
            ),
            LegacyReduxFields::checkbox('show_logo', Travelpayouts::__('Show logo')),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_map_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Price map');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Interactive map with the flight scheme from a specified or automatically defined city of departure. A click anywhere on the card will redirect the user to map.jetradar.com');
    }
}
