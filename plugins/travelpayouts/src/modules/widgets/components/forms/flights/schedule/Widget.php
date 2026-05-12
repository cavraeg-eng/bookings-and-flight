<?php

namespace Travelpayouts\modules\widgets\components\forms\flights\schedule;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;
use Travelpayouts\modules\widgets\components\WidgetDirectionsTrait;

class Widget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;
    use WidgetDirectionsTrait;

    public $promo_id = 2811;
    public $campaign_id = 100;
    public $locale;
    public $origin;
    public $destination;
    public $airline;
    public $shmarker;
    public $target_host;
    public $min_lines;
    public $border_radius;
    public $color_background;
    public $color_text;
    public $color_border;
    public $powered_by = 'true';

    protected $_widget_url = '//tp.media/content?';
    protected $_marker_postfix = '_flights_schedule';

    /**
     * @Inject
     * @param TpScheduleWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function init()
    {
        $this->set_default_options();
    }

    /**
     * Получаем данные из опций и присваиваем
     */
    public function set_default_options()
    {
        $sectionData = $this->section_data;

        $this->subid = $sectionData->get('subid');
        $this->locale = $this->get_locale();
        $this->shmarker = $this->get_marker();
        $this->target_host = $this->get_searchUrl();
        $this->min_lines = $sectionData->get('min_lines');
        $this->border_radius = $sectionData->get('border_radius');
        $this->color_background = $sectionData->get('color_background');
        $this->color_text = $sectionData->get('color_text');
        $this->color_border = $sectionData->get('color_border');
        $this->powered_by = $this->bool_to_string($sectionData->get('powered_by', $this->powered_by));
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin', 'destination'], 'string', 'length' => 3],
            [
                [
                    'origin',
                    'destination',
                    'promo_id',
                    'campaign_id',
                    'locale',
                    'powered_by',
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'airline',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'origin',
                    'destination',
                    'shmarker',
                    'promo_id',
                    'campaign_id',
                    'target_host',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'airline',
                    'powered_by',
                    'min_lines',
                    'border_radius',
                    'color_background',
                    'color_text',
                    'color_border',
                    'locale',
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ]);
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

    public function get_searchUrl()
    {
        if (!$this->_host) {
            $currentLabel = $this->flights_white_label;
            if ($currentLabel && !empty($currentLabel)) {
                $this->_host = $currentLabel . '/flights';
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
                return 'hydra.aviasales.ru';
                break;
            default:
                return 'search.jetradar.com';
        }
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_schedule_widget'];
    }
}
