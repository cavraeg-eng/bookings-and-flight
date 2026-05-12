<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components\columns;

use Travelpayouts\components\grid\columns\GridColumn;

class ColumnDiscount extends GridColumn
{
    /**
     * @inheritDoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        if (is_numeric($value) || is_string($value)) {
            return '-' . $value . '%';
        }

        return '-';
    }

}
