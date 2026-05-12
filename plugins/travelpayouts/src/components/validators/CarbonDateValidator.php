<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\validators;
use Travelpayouts\Vendor\Carbon\Carbon;
use Travelpayouts\Vendor\Carbon\Exceptions\InvalidArgumentException;
use DateTime;
use DateTimeZone;
use Travelpayouts;
use Travelpayouts\components\exceptions\InvalidConfigException;

class CarbonDateValidator extends Validator
{
    /**
     * The format of the outputted date string. See the formatting options below.
     * There are also several predefined date constants that may be used instead, so for example DATE_RSS contains the
     * format string 'D, d M Y H:i:s'. https://www.php.net/manual/ru/datetime.format.php
     * @var string
     */
    public $format;

    /**
     * @var int|string|DateTime|null upper limit of the date. Defaults to null, meaning no upper limit.
     * This can be a unix timestamp or a string representing a date time value.
     * If this property is a string, [[format]] will be used to parse it.
     * @see tooBig for the customized message used when the date is too big.
     */
    public $max;
    /**
     * @var int|string|DateTime|null lower limit of the date. Defaults to null, meaning no lower limit.
     * This can be a unix timestamp or a string representing a date time value.
     * If this property is a string, [[format]] will be used to parse it.
     * @see tooSmall for the customized message used when the date is too small.
     */
    public $min;

    /**
     * @var string user-defined error message used when the value is bigger than [[max]].
     */
    public $tooBig;
    /**
     * @var string user-defined error message used when the value is smaller than [[min]].
     */
    public $tooSmall;

    /**
     * @var string|null user-friendly value of upper limit to display in the error message.
     * If this property is null, the value of [[max]] will be used (before parsing).
     */
    public $maxString;
    /**
     * @var string|null user-friendly value of lower limit to display in the error message.
     * If this property is null, the value of [[min]] will be used (before parsing).
     */
    public $minString;

    /**
     * @var DateTimeZone|null
     */
    public $timeZone;

    /**
     * @var string|Carbon|null the name of the attribute to receive the parsing result.
     * When this property is not null and the validation is successful, the named attribute will
     * receive the parsing result.
     * This can be the same attribute as the one being validated. If this is the case,
     * the original value will be overwritten with the timestamp value after successful validation.
     * Note, that when using this property, the input value will be converted to a unix timestamp, which by definition
     * is in [[$defaultTimeZone|default UTC time zone]], so a conversion from the [[$timeZone|input time zone]] to
     * the default one will be performed.
     * @see outputFormat
     */
    public $outputAttribute;
    /**
     * @var string|null the format to use when populating the [[parsedAttribute]].
     * The format can be specified in the same way as for [[format]].
     * If not set, [[parsedAttribute]] will receive a UNIX timestamp.
     * If [[parsedAttribute]] is not set, this property will be ignored.
     * @see format
     * @see outputAttribute
     */
    public $outputFormat;

    public function init()
    {
        if ($this->min !== null && $this->tooSmall === null) {
            $this->tooSmall = Travelpayouts::_x('{attribute} must be no less than {min}.', 'validator');
        }
        if ($this->max !== null && $this->tooBig === null) {
            $this->tooBig = Travelpayouts::_x('{attribute} must be no greater than {max}.', 'validator');
        }
        if ($this->min !== null) {
            $value = $this->parseDateValue($this->min);
            if ($value === false) {
                throw new InvalidConfigException("Invalid min date value: {$this->min}");
            }
            $this->min = $value;
        }
        if ($this->max !== null) {
            $value = $this->parseDateValue($this->max);
            if ($value === false) {
                throw new InvalidConfigException("Invalid max date value: {$this->max}");
            }
            $this->max = $value;
        }
        if ($this->maxString === null) {
            $this->maxString = $this->max ? $this->max->format($this->format) : null;
        }
        if ($this->minString === null) {
            $this->minString = $this->min ? $this->min->format($this->format) : null;
        }
    }

    protected function validateValue($value)
    {
        $dateValue = $this->parseDateValue($value);
        if ($dateValue === false) {
            return [$this->message, []];
        }

        if ($this->min !== null && $dateValue->isBefore($this->min)) {
            return [$this->tooSmall, ['min' => $this->minString]];
        }

        if ($this->max !== null && $dateValue->isAfter($this->max)) {
            return [$this->tooBig, ['max' => $this->maxString]];
        }

        return null;
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if ($this->isEmpty($value)) {
            if ($this->outputAttribute !== null) {
                $model->{$this->outputAttribute} = null;
            }
            return;
        }

        $dateValue = $this->parseDateValue($value);
        if ($dateValue === false) {
            if ($this->outputAttribute === $attribute) {
                if ($this->outputFormat === null) {
                    if (is_int($value)) {
                        return;
                    }
                } elseif ($this->parseDateValueFormat($value, $this->outputFormat) !== false) {
                    return;
                }
            }
            $this->addError($model, $attribute, $this->message, []);
        } elseif ($this->min !== null && $dateValue->isBefore($this->min)) {
            $this->addError($model, $attribute, $this->tooSmall, ['min' => $this->minString]);
        } elseif ($this->max !== null && $dateValue->isAfter($this->max)) {
            $this->addError($model, $attribute, $this->tooBig, ['max' => $this->maxString]);
        } elseif ($this->outputAttribute !== null) {
            if ($this->outputFormat === null) {
                $model->{$this->outputAttribute} = $dateValue;
            } else {
                $model->{$this->outputAttribute} = $dateValue->format($this->outputFormat);
            }
        }
    }

    /**
     * Parses date string into UNIX timestamp.
     * @param mixed $value string representing date
     * @return Carbon|false a UNIX timestamp or `false` on failure.
     */
    protected function parseDateValue($value)
    {
        return $this->parseDateValueFormat($value, $this->format);
    }

    protected function parseDateValueFormat($value, string $format)
    {
        try {
            if ($value instanceof DateTime) {
                return Carbon::instance($value);
            }

            if (is_numeric($value)) {
                return Carbon::createFromTimestamp($value, $this->timeZone);
            }

            if (!is_string($value)) {
                return false;
            }

            return Carbon::createFromFormat($format, $value, $this->timeZone);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

}
