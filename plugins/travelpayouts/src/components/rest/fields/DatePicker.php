<?php

namespace Travelpayouts\components\rest\fields;

class DatePicker extends BaseValueField
{
    public $type = 'datepicker';
    public $minDate;
    public $maxDate;

    /**
     * @param string $value
     * @return $this
     */
    public function setMinDate($value)
    {
        if (is_string($value)) {
            $this->minDate = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setMaxDate($value)
    {
        if (is_string($value)) {
            $this->maxDate = $value;
        }

        return $this;
    }
}
