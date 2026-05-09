<?php

namespace Travelpayouts\modules\tables\components\flights\priceCalendarWeek;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\components\formatters\PriceFormatter;
use Travelpayouts\modules\tables\components\api\travelpayouts\v2\pricesMonthMatrix\PriceCalendarMatrixApiResponse;

class PriceCalendarWeekApiResponse extends PriceCalendarMatrixApiResponse
{
    /**
     * @var Table
     */
    public $shortcodeModel;

    public function getReturnDateWithAddedDays(): string
    {
        return (new Carbon())->addDays($this->getReturnDateDays())->format('d-m-Y');
    }

    public function getDepartDateWithAddedDays(): string
    {
        return !empty($this->depart_date)
            ? $this->depart_date
            : (new Carbon())->addDays($this->getDepartDateDays())->format('d-m-Y');
    }

    public function buttonVariables(): array
    {
        return [
            'price' => function () {
                return PriceFormatter::getInstance()->format($this->value, $this->shortcodeModel->currency);
            },
        ];
    }

    /**
     * @return int
     */
    protected function getReturnDateDays(): int
    {
        return (int)($this->shortcodeModel->return_date_days ?? '12');
    }

    /**
     * @return int
     */
    protected function getDepartDateDays(): int
    {
        return (int)($this->shortcodeModel->depart_date_days ?? '1');
    }
}
