<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotelSelections;

use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\modules\widgets\components\forms\hotels\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpHotelSelectionsWidget extends Fields
{
    /**
     * @var string
     */
    public $widget_design;
    /**
     * @var string
     */
    public $scalling_width_toggle;
    /**
     * @var array
     */
    public $scalling_width;
    /**
     * @var string
     */
    public $selection_hotel_count;
    /**
     * @var string
     */
    public $powered_by;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return array_merge(
            [
                LegacyReduxFields::widget_preview(
                    $this->optionPath,
                    LegacyReduxFields::WIDGET_PREVIEW_TYPE_SCRIPT,
                    '//www.travelpayouts.com/blissey/{scripts_locale}.js?categories=5stars%2Csea_view%2Cluxury&id=30553&type={{fields.widget_design}}&currency={currency}&host=search.hotellook.com&marker=132474.&limit={{fields.selection_hotel_count}}&powered_by=true'
                ),
                LegacyReduxFields::widget_design(
                    ReduxOptions::widget_design(),
                    ReduxOptions::WIDGET_DESIGN_FULL
                ),
            ],
            LegacyReduxFields::width_toggle(
                $this->id,
                800,
                LegacyReduxFields::get_ID(
                    $this->optionPath,
                    'scalling_width_toggle'
                )
            ),
            [
                LegacyReduxFields::simple_text_slider(
                    'selection_hotel_count',
                    Travelpayouts::__('Number of hotels in the selection'),
                    4,
                    1,
                    15
                ),
                LegacyReduxFields::poweredBy(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_hotel_selections_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Featured hotels');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Automatic or manually created collections of hotels within a given city.');
    }
}
