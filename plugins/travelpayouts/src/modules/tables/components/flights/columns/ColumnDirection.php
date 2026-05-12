<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;

use Travelpayouts\components\formatters\DirectionNameFormatter;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnDirection extends GridColumn
{
    protected $locale = 'en';

    protected $contentBreakWords = false;

    protected function renderDataCellContent($model, $key, $index): ?string
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return $this->getDestinationName($value);
    }

    public function getDestinationName($value): ?string
    {
        return DirectionNameFormatter::getInstance()
            ->getName($value, $this->locale);
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return $this->getDestinationName($value);
    }

}
