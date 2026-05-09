<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\formatters;
use Travelpayouts\Vendor\Carbon\Carbon;
use DateTimeInterface;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\modules\settings\SettingsForm;
use Travelpayouts\traits\SingletonTrait;

class HumanDateFormatter extends BaseInjectedObject
{
    use SingletonTrait;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $settings;

    public function init()
    {
        $this->dateFormat = $this->settings->date_format_radio;
        //  Если выбран формат дат 'custom' берем значение из поля date_format
        if ($this->dateFormat === 'custom') {
            $this->dateFormat = $this->settings->date_format;
        }

        $this->locale = LanguageHelper::tableLocale();
    }

    public function format($value, $locale): ?string
    {
        $value = $this->parseStringIntoDate($value);
        return $value !== null ? $value->locale($locale ?? $this->locale)->translatedFormat($this->dateFormat): null;


    }

    protected function parseStringIntoDate($value): ?Carbon
    {
        if (!$value instanceof DateTimeInterface && !is_string($value) && !is_int($value)) {
            return null;
        }
        return $value instanceof \DateTimeInterface
            ? Carbon::parse($value)
            : Carbon::parse((string)$value);
    }

}
