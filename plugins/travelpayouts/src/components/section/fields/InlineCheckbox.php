<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\section\fields;

class InlineCheckbox extends Checkbox
{
    /**
     * @var string
     */
    public $label;
    /**
     * @var bool
     */
    public $hideTitle = true;

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value): Checkbox
    {
        if (is_string($value)) {
            $this->label = $value;
        }
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setSubtitle($value): InlineCheckbox
    {
        if (is_string($value)) {
            $this->desc = $value;
        }
        return $this;
    }
}
