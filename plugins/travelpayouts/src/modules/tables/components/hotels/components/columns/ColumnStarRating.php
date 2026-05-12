<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components\columns;

use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;

class ColumnStarRating extends GridColumn
{
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if (is_int($value)) {

            $content = '';
            for ($i = 1; $i <= $value; $i++) {
                $content .= '★';
            }

            return Html::tag(
                'span',
                ['class' => 'tp-widget-table-stars'],
                $content
            );
        }
        return $value;
    }

}
