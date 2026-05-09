<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\Vendor\Carbon\CarbonInterface;
use Travelpayouts\components\grid\columns\GridColumn;

class ColumnDuration extends GridColumn
{
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if (is_string($value) || is_int($value)) {
            $endDate = (new Carbon())->addSeconds((int)$value);
            return $endDate->locale('ru')->diffForHumans(new Carbon(), [
                'join' => ' ',
                'parts' => 2,
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ],true);
        }

        return null;
    }
}
