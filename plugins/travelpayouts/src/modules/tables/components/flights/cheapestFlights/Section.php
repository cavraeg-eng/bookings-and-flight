<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\cheapestFlights;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\modules\tables\components\flights\BaseFields;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\src\modules\tables\components\flights\cheapestFlights
 * @property-read string $titlePlaceholder
 * @property-read string $buttonPlaceholder
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
    public $subid = 'direction';

    /**
     * @inheritDoc
     */
    public function enabledColumns(): array
    {
        return [
            ColumnLabels::DEPARTURE_AT,
            ColumnLabels::RETURN_AT,
            ColumnLabels::NUMBER_OF_CHANGES,
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
     * @param string $locale
     * @return string
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.title.The cheapest round-trip tickets from {origin} to {destination}', [], 'tables', $locale);
    }

    /**
     * @param string $locale
     * @return string
     */
    public function buttonPlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.button.Tickets from {price}', [], 'tables', $locale);
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

    public function getLabel(): string
    {
        return Travelpayouts::__('Cheapest flights from origin to destination, round-trip');
    }

    /**
     * @inheritdoc
     */
    public function optionPath(): string
    {
        return 'tp_cheapest_flights_shortcodes';
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }

}
