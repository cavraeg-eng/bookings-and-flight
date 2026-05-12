<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\map;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

class Widget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;

    public $auto_fit_map = 'true';
    public $hide_sidebar = 'true';
    public $hide_reformal = 'true';
    public $disable_googlemaps_ui = 'true';
    public $zoom = 3;
    public $show_filters_icon = 'true';
    public $redirect_on_click = 'true';
    public $small_spinner = 'true';
    public $hide_logo;
    public $direct = 'false';
    public $lines_type = 'TpLines';
    public $cluster_manager = 'TpWidgetClusterManager';
    public $show_tutorial = 'false';
    public $origin_iata;

    protected $_widget_url = '//maps.avs.io/flights/?';
    protected $_marker_postfix = '_map';

    /**
     * @Inject
     * @param TpMapWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function init()
    {
        $sectionData = $this->section_data;
        $hideLogo = !$this->string_to_bool($sectionData->get('show_logo', '0'));
        $direct = $this->string_to_bool($sectionData->get('only_direct_flight', '0'));
        $this->hide_logo = $this->bool_to_string($hideLogo);
        $this->width = $sectionData->get('map_dimensions.width');
        $this->height = $sectionData->get('map_dimensions.height');
        $this->direct = $this->bool_to_string($direct);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                [
                    'origin',
                    'width',
                    'height',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'subid',
                    'direct',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'host',
                    'currency',
                    'auto_fit_map',
                    'hide_sidebar',
                    'hide_reformal',
                    'disable_googlemaps_ui',
                    'zoom',
                    'show_filters_icon',
                    'redirect_on_click',
                    'small_spinner',
                    'lines_type',
                    'cluster_manager',
                    'marker',
                    'show_tutorial',
                    'locale',
                    'origin_iata',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'direct',
                    'hide_logo',
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ]);
    }


    public function set_origin($value)
    {
        $this->origin_iata = $value;
    }

    public function get_marker()
    {
        return parent::get_marker() . '.$69';
    }

    public function get_host()
    {
        if (!$this->_host) {
            $currentLabel = $this->flights_white_label;
            if ($currentLabel && !empty($currentLabel)) {
                $this->_host = $currentLabel . '/map';
            } else {
                $this->_host = $this->get_default_white_label();
            }
        }
        return $this->_host;
    }


    protected function get_default_white_label()
    {
        switch ($this->locale) {
            case Translator::RUSSIAN:
                return 'http://map.aviasales.ru';
                break;
            default:
                return 'http://map.jetradar.com';
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
        return ['tp_map_widget'];
    }
}
