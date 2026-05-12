<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;

class ColumnTrainNumber extends GridColumn
{
    public $contentOptions = [];

    /**
     * @var string
     */
    protected $trainNameAttribute;

    public function init()
    {
        Html::addCssClass($this->headerOptions, Html::classNames([
            'no-sort',
        ]));
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if (is_string($value)) {
            $trainName = $this->getTrainName($model);

            return Html::tagArrayContent('div', ['class' => 'tp-train-data tp-stack-2'], [
                Html::tag('div', ['class' => 'train-train-number'], $value),
                $trainName ? Html::tag('div', ['class' => 'train-train-name'], '"' . $trainName . '"') : null,
            ]);
        }

        return null;
    }

    /**
     * @param $model
     * @return string|null
     */
    protected function getTrainName($model): ?string
    {
        return $this->trainNameAttribute ? $model->{$this->trainNameAttribute} : null;
    }

}
