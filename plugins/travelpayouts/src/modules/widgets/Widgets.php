<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\modules\widgets\components\forms\flights;
use Travelpayouts\modules\widgets\components\forms\hotels;
use Travelpayouts\modules\widgets\components\Section;


/**
 * Class Widgets
 * @package Travelpayouts\modules\widgets
 * @property-read Dot $data
 */
class Widgets extends ModuleRedux
{
    /**
     * @Inject
     * @var Section
     */
    public $section;
    /**
     * @Inject
     * @var flights\map\TpMapWidget
     **/
    public $tp_map_widget;
    /**
     * @Inject
     * @var flights\calendar\TpCalendarWidget
     **/
    public $tp_calendar_widget;
    /**
     * @Inject
     * @var flights\popularRoutes\TpPopularRoutesWidget
     **/
    public $tp_popular_routes_widget;
    /**
     * @Inject
     * @var flights\ducklett\TpDucklettWidget
     **/
    public $tp_ducklett_widget;
    /**
     * @Inject
     * @var flights\subscriptions\TpSubscriptionsWidget
     **/
    public $tp_subscriptions_widget;
    /**
     * @Inject
     * @var hotels\hotel\TpHotelWidget
     **/
    public $tp_hotel_widget;
    /**
     * @Inject
     * @var hotels\hotelMap\TpHotelmapWidget
     **/
    public $tp_hotelmap_widget;
    /**
     * @Inject
     * @var hotels\hotelSelections\TpHotelSelectionsWidget
     **/
    public $tp_hotel_selections_widget;

    /**
     * @inheritdoc
     */
    protected $shortcodeList = [
        components\forms\flights\calendar\Widget::class,
        components\forms\flights\ducklett\Widget::class,
        components\forms\flights\map\Widget::class,
        components\forms\flights\popularRoutes\Widget::class,
        flights\popularRoutes\TpPopularRoutesSummaryWidget::class,
        components\forms\flights\subscriptions\Widget::class,

        components\forms\hotels\hotel\Widget::class,
        components\forms\hotels\hotelMap\Widget::class,
        components\forms\hotels\hotelSelections\Widget::class,

        components\WidgetShortcode::class,
    ];

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }

    public function registerSection()
    {
        /**
         * Сюда можно выставить интересующие условия и включать/отключать рендеринг редакс таблиц
         */
        if ($this->isActive()) {
            $this->section->register();
        }
    }
}
