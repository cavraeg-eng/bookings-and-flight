<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\components\formatters\StationNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper;

class ColumnStation extends GridColumn
{
    public function init()
    {
        HtmlHelper::addCssClass($this->headerOptions, HtmlHelper::classNames([
            'no-sort',
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return StationNameFormatter::getInstance()->format($value) ?? '-';
    }

}
