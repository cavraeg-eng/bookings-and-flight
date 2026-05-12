<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\validators;

use Travelpayouts;
use Travelpayouts\components\exceptions\InvalidConfigException;

class RegularExpressionValidator extends Validator
{
    /**
     * @var string the regular expression to be matched with
     */
    public $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    public $not = false;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->pattern === null) {
            throw new InvalidConfigException('The "pattern" property must be set.');
        }
        if ($this->message === null) {
            $this->message = Travelpayouts::__('{attribute} is invalid.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        $valid = !is_array($value) &&
            ((!$this->not && preg_match($this->pattern, $value))
                || ($this->not && !preg_match($this->pattern, $value)));

        return $valid ? null : [$this->message, []];
    }

}
