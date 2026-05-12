<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\DataView;

use Exception;
use Closure;
use Travelpayouts\Vendor\Glook\YiiGrid\Base\BaseObject;
use Travelpayouts\Vendor\Glook\YiiGrid\DataView\Columns\DataColumn;
use Travelpayouts\Vendor\Glook\YiiGrid\DataView\Columns\Column;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Html;

/**
 * The GridView widget is used to display data in a grid.
 * A basic usage looks like the following:
 * ```php
 * <?= GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         'id',
 *         'name',
 *         'created_at:datetime',
 *         // ...
 *     ],
 * ]) ?>
 * ```
 * The columns of the grid table are configured in terms of [[Column]] classes,
 * which are configured via [[columns]].
 * The look and feel of a grid view can be customized using the large amount of properties.
 * For more details and usage information on GridView, see the [guide article on data widgets](guide:output-data-widgets).
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GridView extends BaseListView
{
    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data column.
     *             Defaults to 'yii\grid\DataColumn'.
     */
    public $dataColumnClass = DataColumn::class;
    /**
     * @var string the caption of the grid table
     * @see captionOptions
     */
    public $caption;
    /**
     * @var array the HTML attributes for the caption element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @see caption
     */
    public $captionOptions = [];
    /**
     * @var array the HTML attributes for the grid table element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $tableOptions = ['class' => 'table table-striped table-bordered'];
    /**
     * @var array the HTML attributes for the container tag of the grid view.
     *            The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'grid-view'];
    /**
     * @var array the HTML attributes for the table header row.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerRowOptions = [];
    /**
     * @var array the HTML attributes for the table footer row.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $footerRowOptions = [];
    /**
     * @var array|Closure the HTML attributes for the table body rows. This can be either an array
     *                    specifying the common HTML attributes for all body rows, or an anonymous function that
     *                    returns an array of the HTML attributes. The anonymous function will be called once for every
     *                    data model returned by [[dataProvider]]. It should have the following signature:
     * ```php
     * function ($model, $key, $index, $grid)
     * ```
     * - `$model`: the current data model being rendered
     * - `$key`: the key value associated with the current data model
     * - `$index`: the zero-based index of the data model in the model array returned by [[dataProvider]]
     * - `$grid`: the GridView object
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $rowOptions = [];
    /**
     * @var Closure an anonymous function that is called once BEFORE rendering each data model.
     *              It should have the similar signature as [[rowOptions]]. The return result of the function
     *              will be rendered directly.
     */
    public $beforeRow;
    /**
     * @var Closure an anonymous function that is called once AFTER rendering each data model.
     *              It should have the similar signature as [[rowOptions]]. The return result of the function
     *              will be rendered directly.
     */
    public $afterRow;
    /**
     * @var bool whether to show the header section of the grid table.
     */
    public $showHeader = true;
    /**
     * @var bool whether to show the footer section of the grid table.
     */
    public $showFooter = false;
    /**
     * @var bool whether to place footer after body in DOM if is true
     * @since 2.0.14
     */
    public $placeFooterAfterBody = false;
    /**
     * @var bool whether to show the grid view if [[dataProvider]] returns no data.
     */
    public $showOnEmpty = true;

    /**
     * @var array grid column configuration. Each array element represents the configuration
     *            for one particular grid column. For example,
     * ```php
     * [
     *     ['__class' => \yii\grid\SerialColumn::class],
     *     [
     *         '__class' => \yii\grid\DataColumn::class, // this line is optional
     *         'attribute' => 'name',
     *         'format' => 'text',
     *         'label' => 'Name',
     *     ],
     *     ['__class' => \yii\grid\CheckboxColumn::class],
     * ]
     * ```
     * If a column is of class [[DataColumn]], the "class" element can be omitted.
     * As a shortcut format, a string may be used to specify the configuration of a data column
     * which only contains [[DataColumn::attribute|attribute]], [[DataColumn::format|format]],
     * and/or [[DataColumn::label|label]] options: `"attribute:format:label"`.
     * For example, the above "name" column can also be specified as: `"name:text:Name"`.
     * Both "format" and "label" are optional. They will take default values if absent.
     * Using the shortcut format the configuration for columns in simple cases would look like this:
     * ```php
     * [
     *     'id',
     *     'amount:currency:Total Amount',
     *     'created_at:datetime',
     * ]
     * ```
     * When using a [[dataProvider]] with active records, you can also display values from related records,
     * e.g. the `name` attribute of the `author` relation:
     * ```php
     * // shortcut syntax
     * 'author.name',
     * // full syntax
     * [
     *     'attribute' => 'author.name',
     *     // ...
     * ]
     * ```
     */
    public $columns = [];
    /**
     * @var string the HTML display when the content of a cell is empty.
     *             This property is used to render cells that have no defined content,
     *             e.g. empty footer.
     * Note that this is not used by the [[DataColumn]] if a data item is `null`. In that case
     * the [[\yii\i18n\Formatter::nullDisplay|nullDisplay]] property of the [[formatter]] will
     * be used to indicate an empty data value.
     */
    public $emptyCell = '&nbsp;';
    /**
     * @var string the layout that determines how different sections of the grid view should be organized.
     *             The following tokens will be replaced with the corresponding section contents:
     * - `{items}`: the list items. See [[renderItems()]].
     */
    public $layout = '{items}';

    /**
     * Initializes the grid view.
     * This method will initialize required property values and instantiate [[columns]] objects.
     */
    public function init()
    {
        parent::init();
        $this->initColumns();
    }

    /**
     * Renders the data models for the grid view.
     * @return string the HTML code of table
     */
    public function renderItems()
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();

        $tableFooter = false;
        $tableFooterAfterBody = false;

        if ($this->showFooter) {
            if ($this->placeFooterAfterBody) {
                $tableFooterAfterBody = $this->renderTableFooter();
            } else {
                $tableFooter = $this->renderTableFooter();
            }
        }

        $content = array_filter([
            $caption,
            $columnGroup,
            $tableHeader,
            $tableFooter,
            $tableBody,
            $tableFooterAfterBody,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }

    /**
     * Renders the caption element.
     * @return bool|string the rendered caption element or `false` if no caption element should be rendered.
     */
    public function renderCaption()
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions);
        }

        return false;
    }

    /**
     * Renders the column group HTML.
     * @return bool|string the column group HTML or `false` if no column group should be rendered.
     */
    public function renderColumnGroup()
    {
        foreach ($this->columns as $column) {
            /* @var $column Column */
            if (!empty($column->options)) {
                $cols = [];
                foreach ($this->columns as $col) {
                    $cols[] = Html::tag('col', '', $col->options);
                }

                return Html::tag('colgroup', implode("\n", $cols));
            }
        }

        return false;
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        return "<thead>\n" . $content . "\n</thead>";
    }

    /**
     * Renders the table footer.
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }

        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string)$key;

        return Html::tag('tr', implode('', $cells), $options);
    }

    /**
     * Creates column objects and initializes them.
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = BaseObject::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::class,
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }

    /**
     * Creates a [[DataColumn]] object based on a string in the format of "attribute:format:label".
     * @param string $text the column specification string
     * @return DataColumn the column instance
     * @throws Exception if the column specification is invalid
     */
    protected function createDataColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new Exception('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
        }

        return BaseObject::createObject([
            'class' => $this->dataColumnClass ?: DataColumn::class,
            'grid' => $this,
            'attribute' => $matches[1],
            'format' => isset($matches[3]) ? $matches[3] : 'text',
            'label' => isset($matches[5]) ? $matches[5] : null,
        ]);
    }

    /**
     * This function tries to guess the columns to show from the given data
     * if [[columns]] are not explicitly specified.
     */
    protected function guessColumns()
    {
        $models = $this->dataProvider->getModels();
        $model = reset($models);
        if (is_array($model) || is_object($model)) {
            foreach ($model as $name => $value) {
                if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                    $this->columns[] = (string)$name;
                }
            }
        }
    }
}
