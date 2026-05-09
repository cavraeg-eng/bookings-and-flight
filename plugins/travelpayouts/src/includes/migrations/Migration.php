<?php

namespace Travelpayouts\includes\migrations;
use Travelpayouts\Vendor\Adbar\Dot;
use DateTime;
use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\Translator;
use Travelpayouts\includes\ReduxConfigurator;
use Travelpayouts\modules\searchForms\models\SearchFormMigrationItem;
use Travelpayouts\modules\searchForms\models\SearchFormModel;
use Travelpayouts\modules\tables\components\flights\ColumnLabels as FlightsColumnLabels;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels as HotelsColumnLabels;
use Travelpayouts\modules\tables\components\railway\ColumnLabels as RailwayColumnLabels;

/**
 * Class Migration
 * @package Travelpayouts\includes\migrations
 * @property-read ReduxConfigurator $redux
 * @property-read Dot $source
 */
class Migration extends BaseObject
{
    const SOURCE_OPTION_NAME = 'travelpayouts_options';
    const IMPORT_DONE_OPTION_NAME = 'travelpayouts_options_import_done';
    const IMPORT_DONE_TRUE = 1;
    const IMPORT_DONE_FALSE = 0;
    const CITY_URL = 'https://autocomplete.travelpayouts.com/places2?locale={lang}&types[]=city&term={term}';
    const HOTEL_CITY_URL = 'https://yasen.hotellook.com/autocomplete?term={term}&lang={lang}';

    public $redux;
    public $source;
    protected $_lang;

    public function init()
    {
        $this->makeSourceDot();
        $this->setLang();
    }

    protected function makeSourceDot()
    {
        $this->source = new Dot($this->source);
    }

    protected function setLang()
    {
        $this->_lang = 'ru';
        if ($this->source->get('local.localization') != 1) {
            $this->_lang = 'en';
        }
    }


    /* Get options */
    private function getOptionValue($key)
    {
        $key = str_replace('{lang}', $this->_lang, $key);

        $value = $this->getSimpleOption($key);
        if ($value === null) {
            $value = $this->getComplexOption($key);
        }

        return $value;
    }

    private function getSimpleOption($key)
    {
        $explodeKey = explode('.', $key);
        $endKey = end($explodeKey);
        $value = $this->source[$key];

        switch ($endKey) {
            case $this->_lang:
                return $this->prepareVariables($value);
            case 'host':
                return $this->getHostValue($value);
            case 'host_hotel':
                return $this->getHostHotelValue($value);
            case 'map_styled':
                return ($value == 1) ? 'custom' : '#FFBC00';
            case 'origin':
            case 'destination':
                return $this->getDirectionValue($value);
            case 'sort_column':
                return $this->getSortByValue($key, $value);
            case 'redirect':
            case 'target_url':
            case 'nofollow':
            case 'only_direct':
            case 'scrollwheel':
            case 'draggable':
            case 'direct':
            case 'paginate_switch':
                return $this->handleCheckbox($value);
            case 'responsive':
                return ($value) ? ReduxOptions::STRETCH_WIDTH_YES : ReduxOptions::STRETCH_WIDTH_NO;
            case 'one_way':
                return ($value == 1) ? 'one_way_ticket' : 'round_trip_ticket';
            case 'filter':
                return ($value == 1) ? 'for_route' : 'for_aircompanies';
            case 'disable_zoom':
            case 'link_without_dates':
            case 'hide_logo':
                return $this->handleInvertedCheckbox($value);
            case 'period_day':
                return [
                    '1' => $this->source[$key . '.from'],
                    '2' => $this->source[$key . '.to']
                ];
            case 'period':
                return $this->period($value);
            case 'distance':
                return ($value == 2) ? 'ml' : 'km';
            case 'after_url':
                return ($value == 1) ? 'results' : 'search';
            case 'hotel_after_url':
                return ($value == 1) ? 'hotel' : 'city';
            case 'script':
                return ($value == 1) ? 'in_footer' : 'in_header';
            case 'view':
                return $this->getArrayOption([
                    '2' => 'hide',
                    '1' => 'compact',
                    '0' => 'default'
                ], $value);
            case 'empty_type':
                $key = str_replace('empty_type', 'empty.type', $key);
                $value = $this->source[$key];

                return $this->getArrayOption([
                    '3' => 'hide',
                    '2' => 'show_message',
                    '1' => 'show_message'
                ], $value);
            case 'localization':
                return $this->getArrayOption([
                    '3' => Translator::THAI,
                    '2' => Translator::ENGLISH,
                    '1' => Translator::RUSSIAN
                ], $value);
            case 'currency_symbol_display':
                return $this->getArrayOption([
                    '4' => 'code_before',
                    '3' => 'code_after',
                    '2' => 'hide',
                    '1' => 'before',
                    '0' => 'after',
                ], $value);
            case 'flights_typography':
                return $this->getFlightsTitleStyles();
        }

        return $value;
    }

    private function getFlightsTitleStyles()
    {
        $tableSettings = Travelpayouts::getInstance()->tables->settingsFlights->data->get('typography');
        if (!empty($tableSettings)) {
            $bold = $this->source['style_table.title_style.bold'];
            $italic = $this->source['style_table.title_style.italic'];
            $fontSize = $this->source['style_table.title_style.font_size'];

            $tableSettings['font-weight'] = '400';
            if (!empty($bold)) {
                $tableSettings['font-weight'] = '700';
            }

            $tableSettings['font-style'] = '';
            if (!empty($italic)) {
                $tableSettings['font-style'] = 'italic';
            }

            $tableSettings['font-family'] = $this->source['style_table.title_style.font_family'];

            if (!empty($fontSize)) {
                $tableSettings['font-size'] = $fontSize . ReduxOptions::FONT_SIZE_UNIT;
                $tableSettings['line-height'] = $fontSize . ReduxOptions::FONT_SIZE_UNIT;
            }
            $tableSettings['color'] = $this->source['style_table.title_style.color'];
        }

        return $tableSettings;
    }

    private function period($value)
    {
        if ($value == 'year') {
            return ReduxOptions::PERIOD_WHOLE_YEAR;
        } elseif ($value == 'current_month') {
            return ReduxOptions::PERIOD_CURRENT_MONTH;
        } else {
            $dateTime = new DateTime($value);

            return strtolower($dateTime->format('F'));
        }
    }

    private function getArrayOption($array, $key, $defaultValue = null)
    {
        if (isset($array[$key]) && !empty($array[$key])) {
            return $array[$key];
        }
        return $defaultValue;
    }

    private function handleInvertedCheckbox($value)
    {
        if ($value == 1) {
            return false;
        }

        return true;
    }

    private function handleCheckbox($value)
    {
        if (empty($value)) {
            return false;
        }

        return true;
    }

    private function prepareVariables($data)
    {
        return preg_replace('/(?!\{)(origin|destination|price|airline)(?!\})/', '{$1}', $data);
    }

    private function getHostValue($value)
    {
        $hosts = ReduxOptions::flight_sources();
        $hostKey = array_search($value, $hosts);

        if (empty($hostKey)) {
            $hostKey = ReduxOptions::FLIGHTS_SOURCE_AVIASALES_RU;
        }

        return $hostKey;
    }

    private function getHostHotelValue($value)
    {
        preg_match('/language\=(.*)/', $value, $params);

        return $this->getArrayOption($params, 1, 'ru-RU');
    }

    private function getSortByValue($key, $value)
    {
        $key = str_replace('sort_column', 'selected', $key);

        return $this->getArrayOption($this->source, $key . '.' . $value, $value);
    }

    private function getColumnLabels($key)
    {
        $keyArray = explode('.', $key);
        $type = array_shift($keyArray);

        switch ($type) {
            case 'shortcodes_hotels':
                return HotelsColumnLabels::getInstance()->getDashboardColumnLabels();
            case 'shortcodes_railway':
                return RailwayColumnLabels::getInstance()->getDashboardColumnLabels();
            default:
                return FlightsColumnLabels::getInstance()->getDashboardColumnLabels();
        }
    }

    private function getComplexOption($key)
    {
        $toSplit = explode('.', $key);

        switch (end($toSplit)) {
            case 'sortable_fields':
                $key = str_replace('sortable_fields', '', $key);
                $columnLabels = $this->getColumnLabels($key);
                $enabled = $this->source->get($key . 'selected', []);
                $fields = $this->source->get($key . 'fields', []);
                $disabled = array_diff($fields, $enabled);

                return [
                    'enabled' => array_merge(
                        ['placebo' => 'placebo'],
                        $this->getColumnsArray($enabled, $columnLabels)
                    ),
                    'disabled' => array_merge(
                        ['placebo' => 'placebo'],
                        $this->getColumnsArray($disabled, $columnLabels)
                    )
                ];
            case 'scalling_width':
                $key = str_replace('scalling_width', '', $key);
                $size = [];
                $size['width'] = $this->source[$key . 'width'];

                $_height = $key . 'height';
                if (isset($this->source[$_height]) && !empty($this->source[$_height])) {
                    $size['height'] = $this->source[$_height];
                }

                return $size;
        }
    }

    private function getColumnsArray($data, $columnLabels)
    {
        $array = [];
        foreach ($data as $value) {
            $array[$value] = $columnLabels[$value];
        }

        return $array;
    }

    private function arrayMapAssoc($array, $keyPrefix, $valuePrefix)
    {
        $r = [];
        foreach ($array as $key => $value) {
            $r[$keyPrefix . $key] = $this->getOptionValue($valuePrefix . $value);
        }
        return $r;
    }

    private function allOptions()
    {
        $options = array_merge(
            $this->priceCalendarMonth(),
            $this->priceCalendarWeek(),
            $this->cheapestFlights(),
            $this->cheapestTicket(),
            $this->cheapestTicketYear(),
            $this->directFlightsRoute(),
            $this->directFlights(),
            $this->popularFromCity(),
            $this->popularDestinationsAirlines(),
            $this->ourSiteSearch(),
            $this->fromOurCityFlight(),
            $this->inOurCityFlight(),
            $this->hotelsDiscount(),
            $this->hotelsDates(),
            $this->railwaySchedule(),

            $this->flightsMap(),
            $this->hotelMap(),
            $this->priceCalendar(),
            $this->priceChangeSub(),
            $this->hotelWidget(),
            $this->popularRoutes(),
            $this->hotelSelections(),
            $this->flightsDucklett(),

            $this->account(),

            $this->settings(),
            $this->settingsLocal(),
            $this->settingsTables()
        );

        return array_filter($options,
            function ($value) {
                return !is_null($value) && $value !== '';
            }
        );
    }

    /* Tables */

    // new - Цены на месяц по направлению, в одну сторону (следующий месяц)
    // old - Цены на месяц по направлению, в одну сторону
    private function priceCalendarMonth()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
        ],
            'tables_flights_tp_price_calendar_month_shortcodes_',
            'shortcodes.1.'
        );
    }

    // new - Билеты по направлению на ближайшие дни
    // old - Билеты по направлению на ближайшие дни
    private function priceCalendarWeek()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
            'depart_date' => 'plus_depart_date',
            'return_date' => 'plus_return_date',
        ],
            'tables_flights_tp_price_calendar_week_shortcodes_',
            'shortcodes.2.'
        );
    }

    // new - Самые дешевые авиабилеты из пункта отправления до пункта назначения, туда-обратно
    // old - Самые дешевые билеты по направлению
    private function cheapestFlights()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_cheapest_flights_shortcodes_',
            'shortcodes.4.'
        );
    }

    // new - Цены на билеты по месяцам (след. месяц)
    // old - Самые дешевые билеты по направлению в этом месяце
    private function cheapestTicket()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
        ],
            'tables_flights_tp_cheapest_ticket_each_day_month_shortcodes_',
            'shortcodes.5.'
        );
    }

    // new - Цены на билеты по месяцам (след. год)
    // old - Цены на билеты по месяцам
    private function cheapestTicketYear()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_cheapest_tickets_each_month_shortcodes_',
            'shortcodes.6.'
        );
    }

    // new - Билеты без пересадок по направлению
    // old - Билеты без пересадок по направлению
    private function directFlightsRoute()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_direct_flights_route_shortcodes_',
            'shortcodes.7.'
        );
    }

    // new - Билеты без пересадок ИЗ
    // old - Билеты без пересадок ИЗ
    private function directFlights()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_direct_flights_shortcodes_',
            'shortcodes.8.'
        );
    }

    // new - Популярные направления из пункта отправления
    // old - Популярные направления из города
    private function popularFromCity()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_popular_routes_from_city_shortcodes_',
            'shortcodes.9.'
        );
    }

    // new - Популярные направления авиакомпании
    // old - Популярные направления авиакомпании
    private function popularDestinationsAirlines()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_flights_tp_popular_destinations_airlines_shortcodes_',
            'shortcodes.10.'
        );
    }

    // new - На нашем сайте искали
    // old - На нашем сайте искали
    private function ourSiteSearch()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
        ],
            'tables_flights_tp_our_site_search_shortcodes_',
            'shortcodes.12.'
        );
    }

    // new - Прямые перелеты из города
    // old - Дешевые перелеты из города
    private function fromOurCityFlight()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
        ],
            'tables_flights_tp_from_our_city_fly_shortcodes_',
            'shortcodes.13.'
        );
    }

    // new - Дешевые перелеты в город
    // old - Дешевые перелеты в город
    private function inOurCityFlight()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
            'stops' => 'transplant',
        ],
            'tables_flights_tp_in_our_city_fly_shortcodes_',
            'shortcodes.14.'
        );
    }

    private function hotelsDiscount()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'assign_dates' => 'link_without_dates',
            'subid' => 'extra_table_marker',
        ],
            'tables_hotels_tp_hotels_selections_discount_shortcodes_',
            'shortcodes_hotels.1.'
        );
    }

    private function hotelsDates()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
            'subid' => 'extra_table_marker',
        ],
            'tables_hotels_tp_hotels_selections_date_shortcodes_',
            'shortcodes_hotels.2.'
        );
    }

    private function railwaySchedule()
    {
        return $this->arrayMapAssoc([
            'title' => 'title.{lang}',
            'title_tag' => 'tag',
            'columns' => 'sortable_fields',
            'button_title' => 'title_button.{lang}',
            'sort_by' => 'sort_column',
            'use_pagination' => 'paginate_switch',
            'pagination_size' => 'paginate',
        ],
            'tables_railway_tp_tutu_shortcodes_',
            'shortcodes_railway.1.'
        );
    }

    /* Widgets */
    private function flightsMap()
    {
        return $this->arrayMapAssoc([
            'only_direct_flight' => 'direct',
            'show_logo' => 'hide_logo',
            'map_dimensions' => 'scalling_width',
        ],
            'widgets_flights_tp_map_widget_',
            'widgets.1.'
        );
    }

    private function hotelMap()
    {
        return $this->arrayMapAssoc([
            'allow_dragging' => 'draggable',
            'enable_zooming' => 'disable_zoom',
            'zooming_during_scrolling' => 'scrollwheel',
            'color_pallete' => 'map_styled',
            //'' => 'zoom',
            'map_dimensions' => 'scalling_width',
            'pins_color' => 'color',
            'texts_color' => 'contrast_color',
        ],
            'widgets_hotels_tp_hotelmap_widget_',
            'widgets.2.'
        );
    }

    private function priceCalendar()
    {
        return $this->arrayMapAssoc([
            'city_departure' => 'origin',
            'city_arrive' => 'destination',
            'travel_time' => 'period_day',
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
            'prices' => 'period',
            'only_direct_flight' => 'only_direct',
            'route_control' => 'one_way',
            //'' => 'powered_by',
        ],
            'widgets_flights_tp_calendar_widget_',
            'widgets.3.'
        );
    }

    private function priceChangeSub()
    {
        return $this->arrayMapAssoc([
            'city_departure' => 'origin',
            'city_arrive' => 'destination',
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
            'bg_pallet' => 'color',
        ],
            'widgets_flights_tp_subscriptions_widget_',
            'widgets.4.'
        );
    }

    private function hotelWidget()
    {
        return $this->arrayMapAssoc([
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
        ],
            'widgets_hotels_tp_hotel_widget_',
            'widgets.5.'
        );
    }

    private function popularRoutes()
    {
        return $this->arrayMapAssoc([
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
            'widget_count' => 'count',
        ],
            'widgets_flights_tp_popular_routes_widget_',
            'widgets.6.'
        );
    }

    private function hotelSelections()
    {
        return $this->arrayMapAssoc([
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
            'selection_hotel_count' => 'limit',
            'widget_design' => 'type',
        ],
            'widgets_hotels_tp_hotel_selections_widget_',
            'widgets.7.'
        );
    }

    private function flightsDucklett()
    {
        return $this->arrayMapAssoc([
            'scalling_width_toggle' => 'responsive',
            'scalling_width' => 'scalling_width',
            'limit_special_offer' => 'limit',
            'widget_design' => 'type',
            'filtering' => 'filter',
        ],
            'widgets_flights_tp_ducklett_widget_',
            'widgets.8.'
        );
    }

    /* Search forms */

    /* Account */
    private function account()
    {
        return $this->arrayMapAssoc([
            'api_token' => 'token',
            'api_marker' => 'marker',
            'flights_domain' => 'white_label',
            'hotels_domain' => 'white_label_hotel',
        ],
            'account_',
            'account.'
        );
    }

    /* Settings */
    private function settings()
    {
        return $this->arrayMapAssoc([
            'date_format' => 'format_date',
            'distance_units' => 'distance',
            'flights_after_url' => 'after_url',
            'hotels_after_url' => 'hotel_after_url',
            'editor_buttons' => 'media_button.view',
            'script_location' => 'script',
            'airline_logo_dimensions' => 'airline_logo_size',
            'redirect' => 'redirect',
            'target_url' => 'target_url',
            'nofollow' => 'nofollow',
            ReduxOptions::cache_time_key_flights() => 'cache_value.flight',
            ReduxOptions::cache_time_key_hotels() => 'cache_value.hotel',
            'table_btn_event' => 'code_ga_ym',
            'table_load_event' => 'code_table_ga_ym',
        ],
            'settings_',
            'config.'
        );
    }

    private function settingsLocal()
    {
        return $this->arrayMapAssoc([
            'flights_source' => 'host',
            'hotels_source' => 'host_hotel',
            'language' => 'localization',
            'currency' => 'currency',
            'currency_symbol_display' => 'currency_symbol_display',
        ],
            'settings_',
            'local.'
        );
    }

    private function settingsTables()
    {
        return $this->arrayMapAssoc([
            'tables_settings_flights_theme' => 'themes_table.name',
            'tables_settings_hotels_theme' => 'themes_table_hotels.name',

            'tables_settings_flights_message_error_switch' => 'shortcodes_settings.empty_type',
            'tables_settings_flights_table_message_error' => 'shortcodes_settings.empty.value.0.{lang}',
            'tables_settings_flights_empty_flights_table_search_form' => 'shortcodes_settings.empty.value.1',

            'tables_settings_flights_typography' => 'flights_typography',
        ],
            '',
            ''
        );
    }


    private function prepareUrl($url, $term)
    {
        return str_replace(['{lang}', '{term}'], [$this->_lang, $term], $url);
    }

    /**
     * @param $value
     * @return false|string|null
     */
    private function getDirectionValue($value)
    {
        $url = $this->prepareUrl(self::CITY_URL, $value);
        $data = $this->getFirstValueFormSuggest($url);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     * @param $url
     * @return array|mixed|null
     */
    private function getFirstValueFormSuggest($url)
    {
        $client = new CachedClient([
            'timeout' => 15,
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => '*'
            ]
        ]);
        $response = $client->get($url);

        if (!$response->isError && $response->statusCode === 200 && $response->json) {
            $data = $response->json;
            if (count($data) > 0) {
                if (isset($data['cities'])) {
                    return array_shift($data['cities']);
                }

                return array_shift($data);
            }
        }

        return [];
    }

    public function import()
    {
        if (!class_exists('Redux_Travelpayouts')) return;

        $this->importSettings();
        $this->importSearchForms();

        update_option(self::IMPORT_DONE_OPTION_NAME, self::IMPORT_DONE_TRUE);
    }

    public function importSettings()
    {
        if (!empty($this->source)) {
            foreach ($this->allOptions() as $key => $value) {
                $this->redux->setOption($key, $value);
            }
        }
    }

    public function importAccount()
    {
        if (!class_exists('Redux_Travelpayouts')) return;

        if (!empty($this->source)) {
            $account = Travelpayouts::getInstance()->account->section->data;
            foreach ($this->account() as $key => $value) {
                if (empty($account->get($key))) {
                    $this->redux->setOption($key, $value);
                }
            }
        }
    }

    public function importSearchForms()
    {
        SearchFormModel::getInstance()->clearCollection();
        $modelList = SearchFormMigrationItem::createFromCollection($this->getSearchFormsData());
        foreach ($modelList as $model) {
            $model->setLocale($this->_lang);
            $searchForm = $model->getSearchFormModel();
            if ($searchForm) {
                $searchForm->save();
            }
        }
    }

    /**
     * @return array
     */
    protected function getSearchFormsData()
    {
        $searchFormsData = $this->source->get('search_forms');
        return empty($searchFormsData) ? (new MigrationQuery())->getSearchForms() : $searchFormsData;
    }
}


