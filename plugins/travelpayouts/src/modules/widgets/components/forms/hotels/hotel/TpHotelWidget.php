<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotel;

use Travelpayouts;
use Travelpayouts\modules\widgets\components\forms\hotels\Fields;
use Travelpayouts\modules\widgets\components\LegacyReduxFields;

class TpHotelWidget extends Fields
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
                    '//www.travelpayouts.com/chansey/iframe.js?hotel_id=361687&locale={locale}&host=search.hotellook.com&marker=132474.&currency={currency}&powered_by=true'
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
            [
                LegacyReduxFields::poweredBy(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_hotel_widget';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Hotel widget');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return Travelpayouts::__('Brief information about a particular hotel with a choice of accommodation dates, search for prices, and go directly to the booking page (bypassing Hotellook).');
    }
}
