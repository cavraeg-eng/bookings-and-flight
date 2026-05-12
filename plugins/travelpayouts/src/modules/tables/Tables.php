<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\components\module\ModuleRedux;

/**
 * Class Tables
 * @package Travelpayouts\src\modules\tables
 * @property-read Dot $data
 */
class Tables extends ModuleRedux
{
    /**
     * @Inject
     * @var components\Section
     */
    public $section;
    /**
     * @Inject
     * @var components\flights\priceCalendarMonth\Section
     */
    public $tp_price_calendar_month_shortcodes;
    /**
     * @Inject
     * @var components\flights\priceCalendarWeek\Section
     */
    public $tp_price_calendar_week_shortcodes;
    /**
     * @Inject
     * @var components\flights\cheapestFlights\Section
     */
    public $tp_cheapest_flights_shortcodes;
    /**
     * @Inject
     * @var components\flights\cheapestTicketEachDayMonth\Section
     */
    public $tp_cheapest_ticket_each_day_month_shortcodes;
    /**
     * @Inject
     * @var components\flights\cheapestTicketsEachMonth\Section
     */
    public $tp_cheapest_tickets_each_month_shortcodes;
    /**
     * @Inject
     * @var components\flights\directFlightsRoute\Section
     */
    public $tp_direct_flights_route_shortcodes;
    /**
     * @Inject
     * @var components\flights\directFlights\Section
     */
    public $tp_direct_flights_shortcodes;
    /**
     * @Inject
     * @var components\flights\popularRoutesFromCity\Section
     */
    public $tp_popular_routes_from_city_shortcodes;
    /**
     * @Inject
     * @var components\flights\popularDestinationsAirlines\Section
     */
    public $tp_popular_destinations_airlines_shortcodes;
    /**
     * @Inject
     * @var components\flights\ourSiteSearch\Section
     */
    public $tp_our_site_search_shortcodes;
    /**
     * @Inject
     * @var components\flights\fromOurCityFly\Section
     */
    public $tp_from_our_city_fly_shortcodes;
    /**
     * @Inject
     * @var components\flights\inOurCityFly\Section
     */
    public $tp_in_our_city_fly_shortcodes;
    /**
     * @Inject
     * @var components\flights\flightSchedule\Section
     */
    public $tp_flights_schedule_shortcodes;
    /**
     * @Inject
     * @var components\hotels\selectionsDate\Section
     */
    public $tp_hotels_selections_date_shortcodes;
    /**
     * @Inject
     * @var components\hotels\selectionsDiscount\Section
     */
    public $tp_hotels_selections_discount_shortcodes;
    /**
     * @Inject
     * @var components\railway\tutu\Section
     */
    public $tp_tutu_shortcodes;
    /**
     * @Inject
     * @var components\settings\FlightsSettingsSection
     */
    public $settingsFlights;
    /**
     * @Inject
     * @var components\settings\HotelSettingsSection
     */
    public $settingsHotels;
    /**
     * @Inject
     * @var components\settings\CustomTableStylesSection
     */
    public $customTableStylesSection;
    /**
     * @Inject
     * @var components\columnTitles\Section
     */
    public $columnTitles;


    /**
     * @inheritdoc
     */
    protected $shortcodeList = [
        components\flights\cheapestFlights\Table::class,
        components\flights\cheapestTicketEachDayMonth\Table::class,
        components\flights\cheapestTicketsEachMonth\Table::class,
        components\flights\directFlights\Table::class,
        components\flights\directFlightsRoute\Table::class,
        components\flights\flightSchedule\Table::class,
        components\flights\fromOurCityFly\Table::class,
        components\flights\inOurCityFly\Table::class,
        components\flights\ourSiteSearch\Table::class,
        components\flights\popularDestinationsAirlines\Table::class,
        components\flights\popularRoutesFromCity\Table::class,
        components\flights\priceCalendarMonth\Table::class,
        components\flights\priceCalendarWeek\Table::class,

        components\hotels\selectionsDate\Table::class,
        components\hotels\selectionsDiscount\Table::class,

        components\railway\tutu\TutuShortcodeModel::class,
    ];

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }

    /**
     * @inheritdoc
     */
    public function registerSection()
    {
        if ($this->isActive()) {
            $this->section->register();
        }
    }
}
