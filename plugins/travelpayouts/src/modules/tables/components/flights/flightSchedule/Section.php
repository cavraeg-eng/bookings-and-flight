<?php

namespace Travelpayouts\modules\tables\components\flights\flightSchedule;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\modules\tables\components\flights\BaseFields;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\src\modules\tables\components\flights\flightSchedule
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
            ColumnLabels::TIME_AND_STOPS,
            ColumnLabels::COMPACT_AIRLINE_LOGO,
            ColumnLabels::FLIGHT_NUMBER,
            ColumnLabels::SCHEDULE,
            ColumnLabels::BUTTON,
        ];
    }

    /**
     * @inheritDoc
     */
    public function disabledColumns(): array
    {
        return [
            ColumnLabels::ROUTE,
            ColumnLabels::FULL_AIRLINE_LOGO,
            ColumnLabels::AIRLINE_NAME,
        ];
    }

    /**
     * @param string $locale
     * @return string
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.title.More than {flights_number} flights{flights_every_day}, from {min_flight_duration}', [], 'tables', $locale);
    }

    /**
     * @param string $locale
     * @return string
     */
    public function buttonPlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.button.Select dates', [], 'tables', $locale);
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
            'button_title' => $this->fieldButtonTitle()
                ->setDesc(''),
            'sort_by',
            'use_pagination',
            'pagination_size',
            'row_link',
            'subid',
        ];
    }

    /**
     * @inheritdoc
     */
    public function optionPath(): string
    {
        return 'tp_flights_schedule_shortcodes';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Flights schedule');
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }
}
