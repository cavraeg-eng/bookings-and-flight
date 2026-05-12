<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\ducklett;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;

class Widget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;

    const FILTER_BY_AIRLINE = '0';
    const FILTER_BY_ROUTE = '1';

    public $limit;
    public $type;
    public $filter;
    public $subid;
    public $powered_by = 'true';
    public $airlines = [];
    public $origin;
    public $destination;

    protected $_marker_postfix = '_specialoff';

    /**
     * @Inject
     * @param TpDucklettWidget $value
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

        $defaultWidgetType = 'slider';
        $defaultFilter = 'for_route';

        $this->width = $sectionData->get('scalling_width.width');
        $this->limit = $sectionData->get('limit_special_offer', 5);
        $this->type = $sectionData->get('widget_design', $defaultWidgetType);

        $this->filter = $sectionData->get('filtering', $defaultFilter) === $defaultFilter
            ? self::FILTER_BY_ROUTE
            : self::FILTER_BY_AIRLINE;
        if (empty($this->type)) {
            $this->type = $defaultWidgetType;
        }

        $this->powered_by = $this->bool_to_string($sectionData->get('powered_by', $this->powered_by));
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [
                [
                    'limit',
                    'type',
                    'marker',
                    'powered_by',
                ],
                'required',
                'strict' => true,
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                ['filter'],
                'filter_type_validator',
                'on' => [self::SCENARIO_DEFAULT],
                'params' => [
                    'allowed' => [
                        self::FILTER_BY_AIRLINE,
                        self::FILTER_BY_ROUTE,
                    ],
                ],
            ],
            [
                [
                    'width',
                    'origin',
                    'destination',
                    'airline',
                    'host',
                    'responsive',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'marker',
                    'host',
                    'currency',
                    'widget_type',
                    'widget_url',
                ],
                'required',
                'on' => [self::SCENARIO_RENDER],
            ],
            [
                [
                    'airline',
                    'origin',
                    'destination',
                    'powered_by',
                ],
                'safe',
                'on' => [self::SCENARIO_RENDER],
            ],
        ]);
    }

    public function filter_type_validator($attribute, $params)
    {
        if (isset($params['allowed'])) {
            $value = $this->$attribute;
            if (!in_array($value, $params['allowed'], true)) {
                $this->add_error($attribute, 'non correct filter value');
            }
        }
    }

    public function render()
    {
        if ($this->validate()) {
            $this->scenario = self::SCENARIO_RENDER;
            $filterValue = (string)$this->filter;
            $widgetClassName = $filterValue === self::FILTER_BY_AIRLINE
                ? WidgetAirlines::class
                : WidgetIata::class;

            $widgetModel = new $widgetClassName();
            $widgetModel->attributes = $this->render_attributes;
            $renderResult = $widgetModel->render();
            if ($renderResult) {
                return $renderResult;
            }
            $this->add_errors($widgetModel->getErrors());
        }
        return $this->render_errors();
    }


    public function get_widget_type()
    {
        return $this->type;
    }

    public function set_airline($value)
    {
        $this->airlines = array_filter(explode(',', $value));
    }

    public function get_airline()
    {
        return $this->airlines;
    }


    public function get_host()
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
        $kzSource = (string)ReduxOptions::FLIGHTS_SOURCE_AVIASALES_KZ;
        $flightsSource = $this->get_settings_data(
            LanguageHelper::optionWithLanguage('flights_source')
        );

        if ($flightsSource === $kzSource) {
            return 'aviasales.kz';
        }

        switch ($this->locale) {
            case Translator::RUSSIAN:
                return 'hydra.aviasales.ru';
            default:
                return 'www.jetradar.com/searches/new';
        }
    }

    public function get_widget_url()
    {
        switch ($this->locale) {
            case Translator::RUSSIAN:
                return '//www.travelpayouts.com/ducklett/scripts.js?';
            case Translator::THAI:
                return '//www.travelpayouts.com/ducklett/scripts_th.js?';
            default:
                return '//www.travelpayouts.com/ducklett/scripts_en.js?';
        }
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_ducklett_widget'];
    }
}
