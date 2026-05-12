<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\selectionsDate;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\includes\Router;
use Travelpayouts\modules\tables\components\hotels\BaseFields;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels;

/**
 * Class Section
 * @package Travelpayouts\src\modules\tables\components\hotels\selectionsDate
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
    public $subid = 'hotelsSelections';

    /**
     * @Inject
     * @var Router
     */
    protected $router;

    /**
     * @inheritDoc
     */
    public function enabledColumns(): array
    {
        return [
            ColumnLabels::NAME,
            ColumnLabels::STARS,
            ColumnLabels::RATING,
            ColumnLabels::PRICE_PN,
            ColumnLabels::BUTTON,
        ];
    }

    /**
     * @inheritDoc
     */
    public function disabledColumns(): array
    {
        return [
            ColumnLabels::DISTANCE,
        ];
    }

    public function init()
    {
        parent::init();

        $availableSectionsController = AvailableSectionsController::getInstance();
        $this->router->addRoute('GET', 'hotels/getAvailableSelections/{id:\d+}', [
            $availableSectionsController,
            'actionGetAvailableSelections',
        ]);
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
        return 'tp_hotels_selections_date_shortcodes';
    }

    /**
     * @inheritDoc
     */
    public function titlePlaceholder($locale = null)
    {
        return Travelpayouts::t('hotel.title.Hotels in {location}: {selection_name} ({dates})', [], 'tables', $locale);
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
        return Travelpayouts::__('Hotel collections for dates');
    }

    /**
     * @inheritDoc
     */
    protected function getShortcode(): ?TableShortcode
    {
        return Table::getInstance();
    }

}
