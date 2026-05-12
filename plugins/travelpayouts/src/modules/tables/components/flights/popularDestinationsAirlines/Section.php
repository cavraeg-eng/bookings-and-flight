<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\popularDestinationsAirlines;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\modules\tables\components\flights\BaseFields;
use Travelpayouts\modules\tables\components\flights\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\src\modules\tables\components\flights\popularDestinationsAirlines
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
    public $sort_by = ColumnLabels::DIRECTION;
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
    public $subid = 'popularAirlines';

    /**
     * @inheritDoc
     */
    public function enabledColumns(): array
    {
        return [
            ColumnLabels::PLACE,
            ColumnLabels::DIRECTION,
            ColumnLabels::BUTTON,
        ];
    }

    /**
     * @inheritDoc
     */
    public function disabledColumns(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'title' => $this->fieldTitle()
                ->setDesc(Travelpayouts::__('Use "airline" variable to add the airlines automatically')),
            'title_tag',
            'columns',
            'button_title' => $this->fieldButtonTitle()->setDesc(''),
            'sort_by',
            'use_pagination',
            'pagination_size',
            'row_link',
            'subid',
            'stops',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_popular_destinations_airlines_shortcodes';
    }

    /**
     * @inheritDoc
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.title.Airline\'s popular flights: {airline}', [], 'tables', $locale);
    }

    /**
     * @inheritDoc
     */
    public function buttonPlaceholder($locale = null)
    {
        return Travelpayouts::t('flights.button.Find tickets', [], 'tables', $locale);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Most popular flights via this airlines');
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }
}
