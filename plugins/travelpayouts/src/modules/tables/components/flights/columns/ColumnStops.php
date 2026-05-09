<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts\components\formatters\StopsNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnStops extends GridColumn
{
    protected $locale = 'en';

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return StopsNameFormatter::getInstance()->format($value, $this->locale);
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return (int)$this->getDataCellValue($model, $key, $index);
    }
}
