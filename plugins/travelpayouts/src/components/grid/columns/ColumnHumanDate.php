<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid\columns;
use Travelpayouts\Vendor\Carbon\Carbon;
use DateTimeInterface;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\modules\settings\SettingsForm;

/**
 * Колонка в которой получаемые значения будут переведены в читаемую дату в нужном формате
 */
class ColumnHumanDate extends GridColumn
{
    public $locale;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $settings;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * Содержимое ячейки выводится в одну строку
     * @var bool
     */
    protected $contentWrap = false;

    /**
     * Содержимое ячейки не разрывается посимвольно
     * @var bool
     */
    protected $contentBreakWords = true;


    public function init()
    {
        if ($this->dateFormat === null) {
            $this->dateFormat = $this->settings->date_format_radio;
            //  Если выбран формат дат 'custom' берем значение из поля date_format
            if ($this->dateFormat === 'custom') {
                $this->dateFormat = $this->settings->date_format;
            }
        }

        if (empty($this->locale)) {
            $this->locale = LanguageHelper::tableLocale();
        }
    }

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        return $value instanceof Carbon ? $value->locale($this->locale)->translatedFormat($this->dateFormat): null;
    }

    protected function getComputedCellValue($model, $value)
    {
        return $this->parseValue($value);
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $dateTime = $this->getDataCellValue($model, $key, $index);
        return $dateTime ? $dateTime->getTimestamp() : null;
    }

    protected function parseValue($value): ?Carbon
    {
        if (!$value instanceof DateTimeInterface && !is_string($value) && !is_int($value)) {
            return null;
        }
        return $value instanceof \DateTimeInterface
            ? Carbon::parse($value)
            : Carbon::parse((string)$value);
    }

}
