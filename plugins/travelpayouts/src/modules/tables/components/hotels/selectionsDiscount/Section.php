<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\selectionsDiscount;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\modules\tables\components\hotels\BaseFields;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\modules\tables\components\hotels\selectionsDiscount
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
    public $sort_by = ColumnLabels::STARS;
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
    public $assign_dates;
    /**
     * @var string
     */
    public $subid = 'hotelsSelections';

    /**
     * @inheritDoc
     */
    public function enabledColumns(): array
    {
        return [
            ColumnLabels::NAME,
            ColumnLabels::STARS,
            ColumnLabels::DISCOUNT,
            ColumnLabels::OLD_PRICE_AND_NEW_PRICE,
            ColumnLabels::BUTTON,
        ];
    }

    /**
     * @inheritDoc
     */
    public function disabledColumns(): array
    {
        return [
            ColumnLabels::PRICE_PN,
            ColumnLabels::OLD_PRICE_AND_DISCOUNT,
            ColumnLabels::DISTANCE,
            ColumnLabels::OLD_PRICE_PN,
            ColumnLabels::RATING,
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
            'assign_dates' => $this->fieldCheckbox()
                ->setTitle(Travelpayouts::__('Assign dates'))
                ->setDefault(true),
            'subid',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tp_hotels_selections_discount_shortcodes';
    }

    /**
     * @inheritDoc
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('hotel.title.Hotels in {location}: {selection_name}', [], 'tables', $locale);
    }

    /**
     * @inheritDoc
     */
    public function buttonPlaceholder($locale = null)
    {
        return Travelpayouts::t('hotel.button.View Hotel', [], 'tables', $locale);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Hotel collection - Discounts');
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }

}
