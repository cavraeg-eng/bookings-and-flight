<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotelSelections;

use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\forms\hotels\HotelLookWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

class Widget extends HotelLookWidgetShortcodeModel
{
    use WidgetDimensionTrait;

    public $id;
    public $type;
    public $limit;
    public $powered_by = 'false';
    public $_categories = [];

    protected $_marker_postfix = '_hotelsselections';

    public function init()
    {
        if ($this->section_data->get('scalling_width_toggle', '0') == '1') {
            $this->width = '100%';
        } else {
            $this->width = $this->section_data->get('scalling_width.width');
        }

        $this->powered_by = $this->bool_to_string($this->section_data->get('powered_by', $this->powered_by));
    }

    /**
     * @Inject
     * @param TpHotelSelectionsWidget $value
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
                    'id',
                    'cat1',
                    'cat2',
                    'cat3',
                    'limit',
                    'type',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'responsive',
                    'subid',
                    'powered_by',
                    'width',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'id',
                    'categories',
                    'type',
                    'currency',
                    'host',
                    'marker',
                    'limit',
                    'width'
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],

            ],
            [
                [
                    'powered_by'
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],

            ],
        ];
    }

    public function set_cat1($value)
    {
        $this->add_category($value);
    }


    public function set_cat2($value)
    {
        $this->add_category($value);
    }


    public function set_cat3($value)
    {
        $this->add_category($value);
    }

    public function add_category($value)
    {
        $this->_categories[] = $value;
    }


    public function get_categories()
    {
        return implode(',', $this->_categories);
    }

    public function render()
    {
        $this->scenario = self::SCENARIO_RENDER;
        if ($this->validate()) {
            $url = implode('', [
                $this->get_widget_url(),
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
                $this->_host = 'search.hotellook.com';
            }
        }
        return $this->_host;
    }


    public function get_widget_url()
    {
        switch ($this->locale) {
            case Translator::RUSSIAN:
                return '//www.travelpayouts.com/blissey/scripts.js?';
            case Translator::THAI:
                return '//www.travelpayouts.com/blissey/scripts_th.js?';
            default:
                return '//www.travelpayouts.com/blissey/scripts_en.js?';
        }
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_hotel_selections_widget'];
    }
}
