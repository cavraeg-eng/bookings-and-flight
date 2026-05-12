<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\directFlightsRoute;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\modules\tables\components\flights\BaseFields;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\src\modules\tables\components\flights\directFlightsRoute
 */
class Section extends BaseFields
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $title_tag;
    /**
     * @var string
     */
    public $button_title;
    /**
     * @var string
     */
    public $sort_by = ColumnLabels::DEPARTURE_AT;
    /**
     * @var string
     */
    public $use_pagination;
    /**
     * @var string
     */
    public $pagination_size;
    /**
     * @var string
     */
    public $row_link;
    /**
     * @var string
     */
    public $subid = 'directionNostops';

    /**
     * @inheritDoc
     */
    public function enabledColumns(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT,
            ColumnLabels::RETURN_AT,
            ColumnLabels::AIRLINE_LOGO,
            ColumnLabels::BUTTON,
        ];
    }

    /**
     * @inheritDoc
     */
    public function disabledColumns(): array
    {
        return [
            ColumnLabels::FLIGHT_NUMBER,
            ColumnLabels::FLIGHT,
            ColumnLabels::PRICE,
            ColumnLabels::AIRLINE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'title',
            'title_tag',
            'columns',
            'button_title',
            'sort_by',
            'use_pagination',
            'pagination_size',
            'row_link',
            'subid',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_direct_flights_route_shortcodes';
    }

    /**
     * @inheritDoc
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.title.Direct flights from {origin} to {destination}', [], 'tables', $locale);
    }

    /**
     * @inheritDoc
     */
    public function buttonPlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.button.Tickets from {price}', [], 'tables', $locale);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Direct flights from origin to destination');
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }
}
