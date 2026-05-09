<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\popularRoutes;

use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

abstract class BaseWidget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;

    public $powered_by = 'false';

    public function rules()
    {
        return [
            [
                [
                    'responsive',
                    'width',
                    'subid',
                    'powered_by',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'marker',
                    'currency',
                    'locale',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'width',
                    'powered_by',
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ];
    }
}
