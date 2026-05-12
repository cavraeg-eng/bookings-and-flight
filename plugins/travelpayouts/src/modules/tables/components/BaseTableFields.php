<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components;

use Travelpayouts;
use Travelpayouts\admin\redux\base\SectionFields;
use Travelpayouts\components\section\fields\BaseField;
use Travelpayouts\components\section\fields\Checkbox;
use Travelpayouts\components\section\fields\Input;
use Travelpayouts\components\section\fields\InputWithVariablesInDescription;
use Travelpayouts\components\section\fields\Select;
use Travelpayouts\components\section\fields\Slider;
use Travelpayouts\components\section\fields\SortBy;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\tables\Tables;

/**
 * Class Fields
 * @package Travelpayouts\modules\tables\components\flights
 * @property-read Tables $module
 */
abstract class BaseTableFields extends SectionFields implements IBaseTableFields
{
    /**
     * Текст заголовка таблицы
     * @var string
     */
    public $title;
    /**
     * Тег заголовка таблицы
     * @var string
     */
    public $title_tag;
    /**
     * Заголовок кнопки
     * @var string
     */
    public $button_title;
    /**
     * Использование пагинации
     * @var string
     */
    public $use_pagination;
    /**
     * Размер страницы пагинации
     * @var string
     */
    public $pagination_size = '10';
    /**
     * Использовать весь ряд как ссылку
     * @var string
     */
    public $row_link;
    /**
     * Список колонок
     * @var array
     */
    public $columns;
    /**
     * Поле для сортировки
     * @var string
     */
    public $sort_by;

    /**
     * Sub id
     * @var string
     */
    public $subid;

    /**
     * @var ColumnSorter
     */
    protected $_fileldColumns;


    /**
     * Список заголовков колонок
     * @return array
     */
    public function dashboardColumnLabels(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function getUsePagination(): bool
    {
        return StringHelper::toBoolean($this->use_pagination);
    }

    /**
     * @return bool
     */
    public function getUseRowAsLink(): bool
    {
        return StringHelper::toBoolean($this->row_link);
    }

    public function getEnabledColumns(): array
    {
        return $this->fieldColumns()->getEnabled();
    }

    /**
     * @return int
     */
    public function getPaginationSize(): int
    {
        return $this->pagination_size ? (int)$this->pagination_size : 10;
    }

    public function fieldColumns(): ColumnSorter
    {
        if (!$this->_fileldColumns) {
            $this->_fileldColumns = new ColumnSorter([
                'tableSection' => $this,
            ]);
        }
        return $this->_fileldColumns;
    }

    public function fieldTitle(): InputWithVariablesInDescription
    {
        return (new InputWithVariablesInDescription())
            ->setID('title')
            ->setTitle(Travelpayouts::__('Table header text'))
            ->setDesc(
                Travelpayouts::__('Use {origin} and {destination} variables to automatically add the city.')
            )->setPlaceholder($this->titlePlaceholder());
    }

    public function fieldTitleTag(): Select
    {
        return $this->fieldSelect()
            ->setID('title_tag')
            ->setTitle(Travelpayouts::__('Table header text tag'))
            ->setSelect2([
                'theme' => 'travelpayouts',
                'allowClear' => false,
                'minimumResultsForSearch' => 10,
            ])
            ->setOptions([
                'div' => Travelpayouts::__('div'),
                'h1' => Travelpayouts::__('h1'),
                'h2' => Travelpayouts::__('h2'),
                'h3' => Travelpayouts::__('h3'),
                'h4' => Travelpayouts::__('h4'),
                'h5' => Travelpayouts::__('h5'),
                'h6' => Travelpayouts::__('h6'),
            ])
            ->setDefault('h3');
    }

    /**
     * @return Checkbox
     */
    public function fieldRowLink(): Checkbox
    {
        return $this->fieldInlineCheckbox()
            ->setID('row_link')
            ->setTitle(Travelpayouts::__('Make table row clickable'));
    }

    /**
     * @return Input
     */
    public function fieldButtonTitle(): InputWithVariablesInDescription
    {
        return (new InputWithVariablesInDescription())
            ->setID('button_title')
            ->setTitle(Travelpayouts::__('Button title'))
            ->setDesc(Travelpayouts::__('{price} variable can be used'))
            ->setPlaceholder($this->buttonPlaceholder());
    }

    /**
     * @return SortBy
     */
    public function fieldSortBy(): SortBy
    {
        $enabledColumnTitles = array_filter(
            $this->fieldColumns()->getDashboardColumnLabels(
                $this->getEnabledColumns()
            )
        );

        return (new SortBy())->setTitle(Travelpayouts::__('Sort by column'))
            ->setSelect2([
                'theme' => 'travelpayouts',
                'allowClear' => false,
                'minimumResultsForSearch' => 10,
            ])->setData($enabledColumnTitles, $this->sort_by);
    }

    /**
     * @return Checkbox
     */
    public function fieldUsePagination(): Checkbox
    {
        return $this->fieldInlineCheckbox()
            ->setID('use_pagination')
            ->setTitle(Travelpayouts::__('Paginate'))
            ->setDefault(true);
    }

    /**
     * @return Slider
     */
    public function fieldPaginationSize(): Slider
    {
        return $this->fieldSlider()
            ->setID('pagination_size')
            ->setTitle(Travelpayouts::__('Rows per page'))
            ->setDefault(10)
            ->setMin(1)
            ->setMax(100);
    }

    /**
     * @return Input
     */
    public function fieldSubId(): Input
    {
        return $this->fieldInput()
            ->setID('subid')
            ->setTitle(Travelpayouts::__('Sub ID'))
            ->setDefault($this->subid);
    }

    /**
     * @return Select
     */
    public function fieldStops(): Select
    {
        return $this->fieldSelect()
            ->setID('stops')
            ->setTitle(Travelpayouts::__('Number of stops'))
            ->setOptions([
                '0' => Travelpayouts::__('All'),
                '1' => Travelpayouts::__('No more than one stop'),
                '2' => Travelpayouts::__('Direct'),
            ])
            ->setSelect2([
                'theme' => 'travelpayouts',
                'allowClear' => false,
                'minimumResultsForSearch' => 10,
            ])
            ->setDefault('0');
    }

    /**
     * @return Slider
     */
    public function fieldDepartureDate(): Slider
    {
        return $this->fieldSlider()
            ->setTitle(Travelpayouts::__('Departure date'))
            ->setDefault(1)
            ->setMin(1)
            ->setMax(100);
    }

    /**
     * @return Slider
     */
    public function fieldReturnDate(): Slider
    {
        return $this->fieldSlider()
            ->setTitle(Travelpayouts::__('Return date'))
            ->setDefault(12)
            ->setMin(1)
            ->setMax(100);
    }

    /**
     * @return BaseField[]
     */
    protected function predefinedFields(): array
    {
        $shortcode = $this->getShortcode();
        $buttonVariables = $shortcode ? $shortcode->getButtonVariables() : [];
        $titleVariables = $shortcode ? $shortcode->getTitlesVariables() : [];

        return array_merge(parent::predefinedFields(), [
            'title' => $this->fieldTitle()->setVariables($titleVariables),
            'title_tag' => $this->fieldTitleTag(),
            'columns' => $this->fieldColumns(),
            'button_title' => $this->fieldButtonTitle()->setVariables($buttonVariables),
            'sort_by' => $this->fieldSortBy(),
            'use_pagination' => $this->fieldUsePagination(),
            'pagination_size' => $this->fieldPaginationSize(),
            'row_link' => $this->fieldRowLink(),
            'subid' => $this->fieldSubId(),
            'stops' => $this->fieldStops(),
            'depart_date' => $this->fieldDepartureDate(),
            'return_date' => $this->fieldReturnDate(),
        ]);
    }

    /**
     * @return TableShortcode|null
     */
    protected function getShortcode(): ?TableShortcode{
        return null;
    }

    /**
     * Список ключей включенных по умолчанию колонок
     * @return array
     */
    public function enabledColumns(): array
    {
        return [];
    }

    /**
     * Список ключей отключенных по умолчанию колонок
     * @return array
     */
    public function disabledColumns(): array
    {
        return [];
    }
}
