<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\components\grid\columns\ColumnHumanDate;
use Travelpayouts\components\HtmlHelper as Html;

/**
 * @method Carbon| null getDataCellValue($model, $key, $index)
 */
class ColumnTime extends ColumnHumanDate
{
    /**
     * Дата с которой будем сравнивать значения для вывода информации о разнице дней
     * @var Carbon
     */
    protected $compareAttribute;

    protected $dateFormat = 'H:i';

    public function renderDataCellContent($model, $key, $index)
    {
        $diffInDays = $this->getDiffWithComparedValueInDays($model, $key, $index);
        $result = parent::renderDataCellContent($model, $key, $index);
        return $diffInDays !== null ?
            implode(' ', [$result, Html::tag('sup', ['class' => 'tp-indicator'], "+{$diffInDays}")]) :
            $result;
    }

    protected function getDiffWithComparedValueInDays($model, $key, $index)
    {
        if (is_string($this->compareAttribute)) {
            $value = $this->getDataCellValue($model, $key, $index);
            $compareValue = $this->parseValue($model->{$this->compareAttribute});

            if ($value && $compareValue) {
                $value = $value->toImmutable()->setHour(0)->setMinute(0);
                $compareValue = $compareValue->toImmutable()->setHour(0)->setMinute(0);
                $result = $compareValue->diffInDays($value);
                return $result > 0 ? $result : null;
            }
        }
        return null;
    }

}
