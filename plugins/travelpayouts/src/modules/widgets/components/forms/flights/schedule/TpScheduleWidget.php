<?php

namespace Travelpayouts\modules\widgets\components\forms\flights\schedule;

use Travelpayouts;
use Travelpayouts\modules\widgets\components\forms\flights\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpScheduleWidget extends Fields
{
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
                    '//tp.media/content?promo_id=2811&shmarker=19812&campaign_id=100&target_host=search.jetradar.com&locale=ru&airline=&min_lines={{fields.min_lines}}&border_radius={{fields.border_radius}}&color_background={{fields.color_background}}&color_text={{fields.color_text}}&color_border={{fields.color_border}}&origin=MOW&destination=BKK&powered_by=true',
                    []
                ),
                LegacyReduxFields::text(
                    'subid',
                    Travelpayouts::__('Sub ID'),
                    '',
                    '',
                    ''
                ),
                LegacyReduxFields::simple_text_slider(
                    'min_lines',
                    Travelpayouts::__('Default rows count'),
                    10,
                    1,
                    100
                ),
                LegacyReduxFields::simple_text_slider(
                    'border_radius',
                    Travelpayouts::__('Border radius, px'),
                    0,
                    0,
                    30
                ),
                LegacyReduxFields::color(
                    'color_background',
                    Travelpayouts::__('Background color'),
                    '',
                    '#FFFFFF',
                    false
                ),
                LegacyReduxFields::color(
                    'color_text',
                    Travelpayouts::__('Text color'),
                    '',
                    '#000000',
                    false
                ),
                LegacyReduxFields::color(
                    'color_border',
                    Travelpayouts::__('Border color'),
                    '',
                    '#FFFFFF',
                    false
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
        return 'tp_schedule_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Schedule widget');
    }

}
