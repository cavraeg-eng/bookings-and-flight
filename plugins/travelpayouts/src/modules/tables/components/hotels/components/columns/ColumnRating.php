<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components\columns;

use Travelpayouts\components\grid\columns\GridColumn;

class ColumnRating extends GridColumn
{
    /**
     * @inheritDoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        return is_string($value) || is_numeric($value) ? (int)$value / 10 : null;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        return (int)parent::getSortOrderValue($model, $key, $index);
    }

}
