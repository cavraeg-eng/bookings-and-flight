<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\calendar;

use DateTime;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts\admin\redux\ReduxOptions as Options;
use Travelpayouts\components\Translator;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;
use Travelpayouts\modules\widgets\components\WidgetDimensionTrait;
use Travelpayouts\modules\widgets\components\WidgetDirectionsTrait;

class Widget extends BaseWidgetShortcodeModel
{
    use WidgetDimensionTrait;
    use WidgetDirectionsTrait;

    public $origin;
    public $destination;
    public $direct = 'false';
    public $one_way = 'false';
    public $period_day_from;
    public $period_day_to;
    public $period;
    public $powered_by = 'true';

    protected $_widget_url = '//www.travelpayouts.com/calendar_widget/iframe.js?';
    protected $_marker_postfix = '_calendar';

    /**
     * @Inject
     * @param TpCalendarWidget $value
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
        $departure = $sectionData->get('city_departure');
        $arrive = $sectionData->get('city_arrive');

        $this->period_day_from = $sectionData->get('travel_time.1');
        $this->period_day_to = $sectionData->get('travel_time.2');

        $this->origin = $this->get_direction_from_autocomplete_json($departure);
        $this->destination = $this->get_direction_from_autocomplete_json($arrive);
        $this->width = $sectionData->get('scalling_width.width');

        $this->period = $this->get_period();
        $this->one_way = $this->bool_to_string($this->get_one_way());
        $this->direct = $this->bool_to_string($this->get_direct());

        $this->powered_by = $this->bool_to_string($sectionData->get('powered_by', $this->powered_by));
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['origin'], 'string', 'length' => 3],
            [
                [
                    'origin',
                    'destination',
                    'direct',
                    'subid',
                    'period_day_from',
                    'period_day_to',
                    'period',
                    'one_way'
                ],
                'required',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'currency',
                    'responsive',
                    'width',
                    'powered_by',
                ],
                'safe',
                'on' => [self::SCENARIO_DEFAULT],
            ],
            [
                [
                    'marker',
                    'destination',
                    'currency',
                    'searchUrl',
                    'one_way',
                    'only_direct',
                    'locale',
                    'period',
                    'range',
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
                return 'hydra.jetradar.com';
        }
    }

    public function get_only_direct()
    {
        return $this->direct;
    }

    public function get_range()
    {
        return $this->period_day_from . ',' . $this->period_day_to;
    }

    /**
     * Получаем из опций значение route_control
     * @return bool
     */
    public function get_one_way()
    {
        $value = $this->section_data->get('route_control', 'round_trip_ticket');
        return $value !== 'round_trip_ticket';
    }

    /**
     * Получаем из опций значение only_direct_flight
     * @return bool
     */
    public function get_direct()
    {
        $value = $this->section_data->get('only_direct_flight');
        return (bool)$value;
    }

    /**
     * Получаем из опций параметры периода
     */
    public function get_period()
    {
        $periodValue = $this->section_data->get('prices', Options::PERIOD_WHOLE_YEAR);

        switch ($periodValue) {
            case Options::PERIOD_CURRENT_MONTH:
                return 'current_month';
                break;
            case Options::PERIOD_WHOLE_YEAR:
                return 'year';
            default:
                return $this->get_period_from_month_name($periodValue);
        }
    }

    /**
     * Переводим наименование месяца в дату формата 'Y-m-d'
     * @param $value
     * @return string
     */
    public function get_period_from_month_name($value)
    {
        $time = new DateTime();
        $monthNumber = (int)date('n', strtotime($value));
        $currentMonth = (int)$time->format('n');
        $currentYear = (int)$time->format('Y');

        if ($currentMonth > $monthNumber) {
            $time->setDate($currentYear + 1, $monthNumber, 1);
        } else {
            $time->setDate($currentYear, $monthNumber, 1);
        }
        return $time->format('Y-m-d');
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_calendar_widget'];
    }
}
