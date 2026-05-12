<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\ducklett;

use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpDucklettWidget extends Fields
{
    const FILTER_TYPE_FOR_AIRCOMPANIES = '0';
    const FILTER_TYPE_FOR_ROUTE = '1';

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
    public $filtering;
    /**
     * @var string
     */
    public $limit_special_offer;
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
                    '//www.travelpayouts.com/ducklett/{scripts_locale}.js?widget_type={{fields.widget_design}}&currency={currency}&host=hydra.aviasales.ru&marker=132474.&limit={{fields.limit_special_offer || 2}}&powered_by=true'
                ),
                LegacyReduxFields::widget_design(
                    ReduxOptions::widget_type(),
                    ReduxOptions::WIDGET_TYPE_SLIDER
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
                LegacyReduxFields::radio(
                    'filtering',
                    Travelpayouts::__('Filtration'),
                    $this->filterTypes(),
                    self::FILTER_TYPE_FOR_ROUTE,
                    LegacyReduxFields::RADIO_LAYOUT_INLINE
                ),
                LegacyReduxFields::simple_text_slider(
                    'limit_special_offer',
                    Travelpayouts::__('Limit for a special offer'),
                    2,
                    1,
                    9
                ),
                LegacyReduxFields::poweredBy(),
            ]
        );
    }

    public function filterTypes(): array
    {
        return [
            self::FILTER_TYPE_FOR_ROUTE => Travelpayouts::__('For route'),
            self::FILTER_TYPE_FOR_AIRCOMPANIES => Travelpayouts::__('For aircompanies'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_ducklett_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Special offers');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Beautiful and convenient visualization of data related to special offers from airlines.');
    }
}
