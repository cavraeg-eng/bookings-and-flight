<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid\columns;
use Travelpayouts\Vendor\Glook\YiiGrid\Data\ArrayDataProvider;
use Travelpayouts\Vendor\Glook\YiiGrid\DataView\Columns\DataColumn;
use ReflectionClass;
use Travelpayouts\components\Container;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\grid\ColumnValuesCollection;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\components\Inflector;
use Travelpayouts\components\Model;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\AnnotationHelper;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\interfaces\Arrayable;

class GridColumn extends DataColumn
{
    public const COLUMN_NOWRAP_CLASSNAME = 'tp-whitespace-nowrap';
    public const TP_TABLE_CELL_NO_BREAK = 'tp-break-normal';

    /**
     * @var ColumnValuesCollection
     */
    protected $valuesCollection;

    /**
     * Содержимое ячейки выводится в несколько строк
     * @var bool
     */
    protected $contentWrap = true;

    /**
     * Содержимое ячейки разрывается посимвольно
     * @var bool
     */
    protected $contentBreakWords = true;

    public function __construct($config = [])
    {
        Container::getInstance()->inject($this);
        $this->valuesCollection = new ColumnValuesCollection();
        parent::__construct($config);
        if (TRAVELPAYOUTS_DEBUG) {
            // Проверяем заполнение необходимых аттрибутов
            $this->checkRequiredAttributes();
        }
    }

    /**
     * @inheritdoc
     */
    protected function getHeaderCellLabel()
    {
        $provider = $this->grid->dataProvider;
        if ($this->label === null) {
            if ($provider instanceof ArrayDataProvider && $provider->modelClass !== null) {
                /** @var Model $modelClass */
                $modelClass = $provider->modelClass;
                $model = $modelClass::getInstance();
                $label = $model->get_attribute_label($this->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    /* @var $model TableShortcode */
                    $label = $model->get_attribute_label($this->attribute);
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
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $valuesCollection = $this->valuesCollection;
        $value = $valuesCollection->getValue($model, $index, $key);

        if (!$value) {
            $value = parent::getDataCellValue($model, $key, $index);

            if (!$value && $model instanceof Arrayable) {
                $value = ArrayHelper::getFirst($model->toArray([$this->attribute]));
            }

            if ($value && $computedValue = $this->getComputedCellValue($model, $value)) {
                $value = $computedValue;
            }

            $valuesCollection->addValue($model, $key, $index, $value);
        }
        return $value;
    }

    /**
     * Returns the computed data cell value.
     * @return mixed|null
     */
    protected function getComputedCellValue($model, $value)
    {
        return null;
    }

    /**
     * Renders a data cell.
     * @param mixed $model the data model being rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data item among the item array returned by
     *     [[GridView::dataProvider]].
     * @return string the rendering result
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->getDataCellOptions($model, $key, $index);

        return Html::tag('td', $options, $this->renderDataCellContent($model, $key, $index));
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell()
    {
        return Html::tag('th', $this->headerOptions, $this->renderHeaderCellContent());
    }

    /**
     * @param $model
     * @param $key
     * @param int $index
     * @return array|\Closure|mixed
     */
    protected function getDataCellOptions($model, $key, int $index)
    {
        if ($this->contentOptions instanceof \Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        Html::addCssClass($options, Html::classNames(array_filter([
            'tp-widget-table-cell',
            $this->attribute ? Inflector::camel2id("tp-widget-table-cell-$this->attribute") : null,
            !$this->contentWrap ? self::COLUMN_NOWRAP_CLASSNAME : null,
            !$this->contentBreakWords ? self::TP_TABLE_CELL_NO_BREAK : null,
        ])));

        $options = array_merge($options, [
            'data-label' => $this->getHeaderCellLabel(),
        ]);

        if ($sortOrderValue = $this->getSortOrderValue($model, $key, $index)) {
            $options = array_merge($options, [
                'data-order' => $sortOrderValue,
            ]);
        }
        return $options;
    }

    /**
     * Объединяем опции колонки
     * @param array $original
     * @param array $override
     * @return mixed|null
     */
    public static function mergeOptions(array $original, array $override)
    {
        $htmlOptionKeys = [
            'headerOptions',
            'contentOptions',
            'contentOptions',
        ];

        /**
         * Объединяем названия классов из original с override
         */
        foreach ($htmlOptionKeys as $htmlOptionKey) {
            if (isset($original[$htmlOptionKey], $override[$htmlOptionKey]['class'])) {
                Html::addCssClass($original[$htmlOptionKey], $override[$htmlOptionKey]['class']);
                unset($override[$htmlOptionKey]['class']);
            }
        }

        return ArrayHelper::merge($original, $override);
    }

    /**
     * Проверяем что аттрибуты помеченные как @required в phpdoc не пусты
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function checkRequiredAttributes(): void
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getProperties() as $property) {
            $property = $reflectionClass->getProperty($property->name);
            $docblock = $property->getDocComment();
            $annotations = AnnotationHelper::parseAnnotations($docblock);
            if (isset($annotations['required']) &&
                $this->{$property->name} === null
            ) {
                throw new InvalidConfigException(get_class($this) . " it seems that you forgot to pass required '$property->name' property");
            }
        }
    }

    /**
     * Значение используемое для сортировки
     * @param $model
     * @param $key
     * @param int $index
     * @return string|integer|float|null
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return $this->getDataCellValue($model, $key, $index);
    }

}
