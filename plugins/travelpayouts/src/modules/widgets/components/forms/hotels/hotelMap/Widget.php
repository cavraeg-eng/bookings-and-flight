<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels\hotelMap;

use Travelpayouts\modules\widgets\components\forms\hotels\HotelLookWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

/**
 * Class Widget
 * @package Travelpayouts\src\modules\widgets\components\forms\hotels\hotelMap
 * @property string $width
 * @property string $height
 */
class Widget extends HotelLookWidgetShortcodeModel
{
    use WidgetDimensionTrait;


    public $color;
    public $changeflag = '0';
    public $draggable;
    public $map_styled = true;
    public $map_color;
    public $contrast_color;
    public $disable_zoom;
    public $base_diameter = 16;
    public $scrollwheel;
    public $lat;
    public $lng;
    public $zoom;

    protected $_widget_url = '//maps.avs.io/hotels?';
    protected $_marker_postfix = '_hotelsmap';

    /**
     * @Inject
     * @param TpHotelmapWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function init()
    {
        $sectionData = $this->section_data;
        $enableZoom = !$this->string_to_bool($sectionData->get('enable_zooming', 0));
        $scrollWheel = $this->string_to_bool($sectionData->get('zooming_during_scrolling', 0));
        $draggable = $this->string_to_bool($sectionData->get('allow_dragging', 0));
        $this->disable_zoom = $this->bool_to_string($enableZoom);
        $this->zoom = $sectionData->get('zoom', 12);
        $this->scrollwheel = $this->bool_to_string($scrollWheel);
        $this->draggable = $this->bool_to_string($draggable);

        $mapColor = $sectionData->get('color_pallete');
        if($mapColor == 'custom') {
            $this->map_color = $sectionData->get('pins_color');
            $this->color = $sectionData->get('pins_color');
            $this->contrast_color = $sectionData->get('texts_color');
        } else {
            $this->map_color = $mapColor;
            $this->color = $mapColor;
            $this->contrast_color = '#ffffff';
        }
    }

    public function rules()
    {
        return [
            [
                [
                    'coordinates',
                    'width',
                    'height',
                    'zoom',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'subid',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'color',
                    'locale',
                    'marker',
                    'draggable',
                    'map_styled',
                    'map_color',
                    'contrast_color',
                    'disable_zoom',
                    'base_diameter',
                    'scrollwheel',
                    'host',
                    'lat',
                    'lng',
                    'zoom',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'changeflag',
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ];
    }

    public function set_coordinates($value)
    {
        $coordinates = explode(',', $value);
        if (count($coordinates) === 2) {
            $this->lat = trim($coordinates[0]);
            $this->lng = trim($coordinates[1]);
        }
    }

    public function get_marker()
    {
        return parent::get_marker() . '.$69';
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

    public function render()
    {
        $this->scenario = self::SCENARIO_RENDER;
        if ($this->validate()) {
            $url = implode('', [
                $this->widget_url,
                http_build_query($this->render_attributes),
            ]);

            $iframeHtmlOptions = [
                'width' => $this->width,
                'height' => $this->height,
            ];

            return $this->render_iframe($url, [], $iframeHtmlOptions);

        }
        return $this->render_errors();
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_hotelmap_widget'];
    }
}
