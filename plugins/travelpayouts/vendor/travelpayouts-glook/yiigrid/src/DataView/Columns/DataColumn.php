<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\DataView\Columns;

use Closure;
use Travelpayouts\Vendor\Glook\YiiGrid\Data\ArrayDataProvider;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Html;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Arrays\ArrayHelper;

/**
 * DataColumn is the default column type for the [[GridView]] widget.
 * It is used to show data columns and allows [[enableSorting|sorting]] and [[filter|filtering]] them.
 * A simple data column definition refers to an attribute in the data model of the
 * GridView's data provider. The name of the attribute is specified by [[attribute]].
 * By setting [[value]] and [[label]], the header and cell content can be customized.
 * A data column differentiates between the [[getDataCellValue|data cell value]] and the
 * [[renderDataCellContent|data cell content]]. The cell value is an un-formatted value that
 * may be used for calculation, while the actual cell content is a [[format|formatted]] version of that
 * value which may contain HTML markup.
 * For more details and usage information on DataColumn, see the [guide article on data widgets](guide:output-data-widgets).
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DataColumn extends Column
{
    /**
     * @var string the attribute name associated with this column. When neither [[content]] nor [[value]]
     *             is specified, the value of the specified attribute will be retrieved from each data model and displayed.
     * Also, if [[label]] is not specified, the label associated with the attribute will be displayed.
     */
    public $attribute;
    /**
     * @var string label to be displayed in the [[header|header cell]] and also to be used as the sorting
     *             link label when sorting is enabled for this column.
     *             If it is not set and the models provided by the GridViews data provider are instances
     *             of [[\Yiisoft\Db\ActiveRecord]], the label will be determined using [[\Yiisoft\Db\ActiveRecord::getAttributeLabel()]].
     *             Otherwise [[\Yiisoft\Strings\Inflector::camel2words()]] will be used to get a label.
     */
    public $label;
    /**
     * @var bool whether the header label should be HTML-encoded.
     * @see label
     * @since 2.0.1
     */
    public $encodeLabel = true;
    /**
     * @var string|Closure an anonymous function or a string that is used to determine the value to display in the current column.
     * If this is an anonymous function, it will be called for each row and the return value will be used as the value to
     * display for every data model. The signature of this function should be: `function ($model, $key, $index, $column)`.
     * Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being rendered
     * and `$column` is a reference to the [[DataColumn]] object.
     * You may also set this property to a string representing the attribute name to be displayed in this column.
     * This can be used when the attribute to be displayed is different from the [[attribute]] that is used for
     * sorting and filtering.
     * If this is not set, `$model[$attribute]` will be used to obtain the value, where `$attribute` is the value of [[attribute]].
     */
    public $value;
    /**
     * @var string|array|Closure in which format should the value of each data model be displayed as (e.g. `"raw"`, `"text"`, `"html"`,
     *                           `['date', 'php:Y-m-d']`). Supported formats are determined by the [[GridView::formatter|formatter]] used by
     *                           the [[GridView]]. Default format is "text" which will format the value as an HTML-encoded plain text when
     *                           [[\yii\i18n\Formatter]] is used as the [[GridView::$formatter|formatter]] of the GridView.
     * @see \yii\i18n\Formatter::format()
     */
    public $format = 'text';

    /**
     * {@inheritdoc}
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || $this->label === null && $this->attribute === null) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        return $label;
    }

    /**
     * {@inheritdoc].
     * @since 2.0.8
     */
    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;

        if ($this->label === null) {
            if ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                $modelClass = $provider->modelClass;
                $model = $modelClass::instance();
                $label = $model->getAttributeLabel($this->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model Model */
                    $label = $model->getAttributeLabel($this->attribute);
                } else {
                    $label = $this->attribute;
                }
            }
        } else {
            $label = $this->label;
        }

        return $label;
    }


    /**
     * Returns the data cell value.
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            }

            return call_user_func($this->value, $model, $key, $index, $this);
        }
        if ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null) {
            return $this->getDataCellValue($model, $key, $index);
        }

        return parent::renderDataCellContent($model, $key, $index);
    }
}
