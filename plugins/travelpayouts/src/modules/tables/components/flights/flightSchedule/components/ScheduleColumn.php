<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\flightSchedule\components;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\helpers\ArrayHelper;

class ScheduleColumn extends GridColumn
{
    public $locale = 'en';

    protected $contentWrap = false;

    /**
     * @var int
     */
    protected $firstDayOfWeek;

    /**
     * @var string[]
     */
    protected $_translationPhrases;

    public function init()
    {
        $this->firstDayOfWeek = (new Carbon())->locale($this->locale)->firstWeekDay;
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var bool[] $flightDays */
        $flightDays = $this->getDataCellValue($model, $key, $index);

        if (is_array($flightDays) && ArrayHelper::isIndexed($flightDays)) {
            return Html::tag('span', [
                'class' => 'tp-flex tp-items-center tp-gap-1 tp-flex-nowrap',
            ], $this->renderScheduleDays($flightDays));
        }

        return null;
    }

    /**
     * @param $flightDays
     * @return string
     */
    protected function renderScheduleDays($flightDays): string
    {
        $result = '';
        if (is_array($flightDays)) {
            $daysOutput = [];
            $translationPhrases = $this->getTranslationPhrases();
            foreach ($flightDays as $flightDayIndex => $isActive) {
                if (isset($translationPhrases[$flightDayIndex])) {
                    ['short' => $shortDayName, 'full' => $fullDayName] = $translationPhrases[$flightDayIndex];
                    $daysOutput[] = Html::tag(
                        'span',
                        [
                            'class' => Html::classNames([
                                !$isActive ? 'tp-opacity-40 tp-line-through': null,
                            ]),
                            'title' => $fullDayName,
                        ],
                        $shortDayName
                    );
                }
            }
            $result = implode(' ', $daysOutput);
        }
        return $result;
    }

    protected function getTranslationPhrases(): array
    {
        $date = ($this->firstDayOfWeek === 0 ?
            Carbon::parse('sunday')->locale($this->locale) :
            Carbon::parse('monday')->locale($this->locale))->toImmutable();

        if (!$this->_translationPhrases) {
            $result = [];
            for ($i = 0; $i <= 6; $i++) {
                $dayName = $date->addDays($i)->dayName;
                $result[] = [
                    'short' => mb_strtoupper(mb_substr($dayName, 0, 1)),
                    'full' => ucfirst($dayName),
                ];
            }
            $this->_translationPhrases = $result;
        }
        return $this->_translationPhrases;
    }

    protected function getActiveDaysCount($value): int
    {
        return is_array($value) ? count(array_filter($value)) : 0;
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        return is_array($value) ? $this->getActiveDaysCount($value) : null;
    }

}
