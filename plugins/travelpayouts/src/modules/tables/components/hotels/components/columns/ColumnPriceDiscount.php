<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components\columns;

use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\components\HtmlHelper as Html;

class ColumnPriceDiscount extends \Travelpayouts\components\grid\columns\ColumnPrice
{
    /**
     * @required
     * @var string
     */
    public $oldPriceAttribute;

    /**
     * @var string
     */
    public $discountAttribute;

    /**
     * @required
     * @var string
     */
    public $attribute;

    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        $oldPrice = $this->getOldPrice($model);

        if ($this->isValidPrice($value) && $this->isValidPrice($oldPrice)) {
            $discountSize = $this->getDiscountSize($model);
            return Html::tagArrayContent('span', ['style'=>'display: flex; flex-direction: column;'], [
                $this->renderOldPrice($oldPrice),
                $this->renderDiscount($discountSize),
                $this->renderPrice($value),
            ]);
        }

        return '-';
    }

    /**
     * @param $model
     * @return string|numeric|null
     */
    protected function getOldPrice($model)
    {
        return $model->{$this->oldPriceAttribute} ?? null;
    }

    /**
     * @param $model
     * @return string|numeric|null
     */
    protected function getDiscountSize($model)
    {
        if ($this->discountAttribute) {
            return $model->{$this->discountAttribute} ?? null;
        }
        return null;
    }

    protected function isValidPrice($value): bool
    {
        return is_string($value) || is_numeric($value);
    }

    protected function renderOldPrice($value): string
    {
        return Html::tag(
            'span',
            ['class' => 'tp-widget-table-cross-out'],
            $this->renderPrice($value)
        );
    }

    protected function renderDiscount($value): ?string
    {
        return $value ? '-' . $value . '%' : null;
    }

    protected function renderPrice($value)
    {
        return Html::tag('span', ['style' => 'margin: 5px 0 0 0;'], PriceFormatter::getInstance()
            ->format($value, $this->currency));
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
