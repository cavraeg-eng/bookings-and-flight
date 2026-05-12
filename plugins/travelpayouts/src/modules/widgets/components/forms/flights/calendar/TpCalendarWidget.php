<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\calendar;

use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpCalendarWidget extends Fields
{
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
    public $city_departure;
    /**
     * @var string
     */
    public $city_arrive;
    /**
     * @var string
     */
    public $prices;
    /**
     * @var array
     */
    public $travel_time;
    /**
     * @var string
     */
    public $route_control;
    /**
     * @var string
     */
    public $only_direct_flight;
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
                    '//www.travelpayouts.com/calendar_widget/iframe.js?marker=19812.&origin=MOW&destination=BKK&currency={currency}&searchUrl=hydra.aviasales.ru&one_way={{toBoolean(fields.route_control === "one_way_ticket") || false}}&only_direct={{toBoolean(fields.only_direct_flight) || false}}&locale={locale}&period=year&range=7,14&powered_by=true',
                    [
                        'width' => '{{fields.scalling_width.width}}',
                    ]
                ),
            ],
            LegacyReduxFields::width_toggle(
                $this->id,
                661,
                LegacyReduxFields::get_ID(
                    $this->optionPath,
                    'scalling_width_toggle'
                )
            ),
            LegacyReduxFields::flight_directions(),
            [
                LegacyReduxFields::select(
                    'prices',
                    Travelpayouts::__('Prices for the period'),
                    ReduxOptions::price_for_period(),
                    'current_month'
                ),
                LegacyReduxFields::slider(
                    'travel_time',
                    Travelpayouts::__('Trip duration'),
                    [
                        1 => 7,
                        2 => 14,
                    ],
                    0,
                    1,
                    30,
                    'input',
                    2
                ),
                LegacyReduxFields::radio(
                    'route_control',
                    Travelpayouts::__('Route control'),
                    [
                        'one_way_ticket' => Travelpayouts::__('One way ticket'),
                        'round_trip_ticket' => Travelpayouts::__('Round trip ticket'),
                    ],
                    false,
                    LegacyReduxFields::RADIO_LAYOUT_INLINE
                ),
                LegacyReduxFields::checkbox('only_direct_flight', Travelpayouts::__('Only direct flight')),
                LegacyReduxFields::poweredBy(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_calendar_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Low price calendar');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Minimum prices for flights in the selected direction on a variety of dates.');
    }
}
