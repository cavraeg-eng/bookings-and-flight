<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\rest\models\BaseGutenbergRestCampaign;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;

class GutenbergRestCampaign extends BaseGutenbergRestCampaign
{
    /**
     * @Inject
     * @var cheapestFlights\Table
     */
    public $tp_cheapest_flights_shortcodes;
    /**
     * @Inject
     * @var cheapestTicketEachDayMonth\Table
     */
    public $tp_cheapest_ticket_each_day_month_shortcodes;
    /**
     * @Inject
     * @var cheapestTicketsEachMonth\Table
     */
    public $tp_cheapest_tickets_each_month_shortcodes;
    /**
     * @Inject
     * @var directFlights\Table
     */
    public $tp_direct_flights_shortcodes;
    /**
     * @Inject
     * @var directFlightsRoute\Table
     */
    public $tp_direct_flights_route_shortcodes;
    /**
     * @Inject
     * @var fromOurCityFly\Table
     */
    public $tp_from_our_city_fly_shortcodes;
    /**
     * @Inject
     * @var inOurCityFly\Table
     */
    public $tp_in_our_city_fly_shortcodes;
    /**
     * @Inject
     * @var ourSiteSearch\Table
     */
    public $tp_our_site_search_shortcodes;
    /**
     * @Inject
     * @var popularRoutesFromCity\Table
     */
    public $tp_popular_routes_from_city_shortcodes;
    /**
     * @Inject
     * @var priceCalendarMonth\Table
     */
    public $tp_price_calendar_month_shortcodes;
    /**
     * @Inject
     * @var priceCalendarWeek\Table
     */
    public $tp_price_calendar_week_shortcodes;
    /**
     * @Inject
     * @var flightSchedule\Table
     */
    public $tp_flights_schedule_shortcodes;

    /**
     * @inheritDoc
     */
    protected function campaignId()
    {
        return CampaignsSubscriptionsEndpoint::AVIASALES_ID;
    }
}
