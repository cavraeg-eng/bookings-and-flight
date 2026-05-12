<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotel;

use Travelpayouts\modules\widgets\components\forms\hotels\HotelLookWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

class Widget extends HotelLookWidgetShortcodeModel
{
    use WidgetDimensionTrait;

    protected $_marker_postfix = '_hotel';
    public $hotel_id;
    public $powered_by = 'false';

    protected $_widget_url = '//www.travelpayouts.com/chansey/iframe.js?';

    /**
     * @Inject
     * @param TpHotelWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function rules()
    {
        return [
            [
                [
                    'hotel_id',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
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
                    'hotel_id',
                    'marker',
                    'currency',
                    'host',
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

    public function init()
    {
        if ($this->section_data->get('scalling_width_toggle', '0') == '1') {
            $this->width = '100%';
        } else {
            $this->width = $this->section_data->get('scalling_width.width');
        }

        $this->powered_by = $this->bool_to_string($this->section_data->get('powered_by', $this->powered_by));
    }


    public function render()
    {
        $this->scenario = self::SCENARIO_RENDER;
        if ($this->validate()) {
            $url = implode('', [
                $this->widget_url,
                http_build_query($this->render_attributes),
            ]);

            return $this->render_script($url);

        }
        return $this->render_errors();
    }

    public function get_host()
    {
        if (!$this->_host) {
            $currentLabel = $this->hotels_white_label;
            if ($currentLabel && !empty($currentLabel)) {
                $this->_host = $currentLabel . '/hotels';
            } else {
                $this->_host = $this->get_default_white_label();
            }
        }
        return $this->_host;
    }


    protected function get_default_white_label()
    {
        switch ($this->locale) {
            case 'ru':
                return 'hotellook.ru';
                break;
            default:
                return 'hotellook.com';
        }
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_hotel_widget'];
    }
}
