<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid\columns;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\components\HtmlHelper as Html;

class ColumnPrice extends GridColumn
{
    protected $contentWrap = false;
    /**
     * @required
     * @var string
     */
    protected $currency;

    public $contentOptions = [
        'class' => 'tp-break-words tp-whitespace-nowrap',
    ];

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        $currencyCode = strtolower($this->currency);
        return Html::tag('span', ['class' => self::COLUMN_NOWRAP_CLASSNAME], PriceFormatter::getInstance()
            ->format($value, $currencyCode) ?? '-');
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return $value !== null ? (int)$value : 99999999;
    }

}
