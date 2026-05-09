<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\modules\settings\SettingsForm;
use Travelpayouts\traits\SingletonTrait;

class PriceFormatter extends BaseInjectedObject
{
    use SingletonTrait;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    /**
     * @var string
     */
    protected $currencySymbolPosition;

    public function init()
    {
        $this->currencySymbolPosition = $this->globalSettings->currency_symbol_display;
    }

    public function format($value, $currencyCode): ?string
    {
        if ($value && (is_string($value) || is_float($value) || is_numeric($value))) {
            $formattedPrice = $this->formatPrice($value);
            return $formattedPrice ? $this->positionPrice($formattedPrice, $currencyCode) : null;
        }

        return null;
    }

    protected function getCurrencyIcon($currencyCode): string
    {
        return Html::tag(
            'span',
            ['class' => 'currency_font'],
            Html::tag(
                'i',
                ['class' => 'currency_font--' . strtolower($currencyCode)],
                ''
            )
        );
    }

    /**
     * Форматируем цену с разделением групп
     * @param $price
     * @return string
     */
    protected function formatPrice($price): ?string
    {
        if ($price && (is_string($price) || is_float($price) || is_numeric($price))) {
            return number_format((float)$price, 0, '.', ' ');
        }
        return null;
    }

    protected function positionPrice($price, $currencyCode): string
    {
        $currencyIcon = $this->getCurrencyIcon($currencyCode);
        switch ($this->currencySymbolPosition) {
            case 'after':
            default:
                return $price . ' ' . $currencyIcon;
            case 'before':
                return $currencyIcon . ' ' . $price;
            case 'hide':
                return $price;
            case 'code_after':
                return $price . ' ' . $currencyCode;
            case 'code_before':
                return $currencyCode . ' ' . $price;
        }
    }
}
