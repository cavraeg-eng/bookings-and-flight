<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\popularRoutes;

use Travelpayouts;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpPopularRoutesWidget extends Fields
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
    public $widget_count;
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
                    '//www.travelpayouts.com/weedle/widget.js?marker=132474&host=hydra.aviasales.ru&locale={locale}&currency={currency}&powered_by=true&destination=BKK&destination_name=%D0%91%D0%B0%D0%BD%D0%B3%D0%BA%D0%BE%D0%BA'
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
                    'widget_count',
                    Travelpayouts::__('Number of widgets added to an entry'),
                    [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                    ],
                    1,
                    LegacyReduxFields::RADIO_LAYOUT_INLINE
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
        return 'tp_popular_routes_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Top destinations');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Prices for flights to the destination from the city of departure and other popular destinations.');
    }
}
