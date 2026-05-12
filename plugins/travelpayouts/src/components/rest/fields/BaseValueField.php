<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\fields;

/**
 * Поля ссылающиеся на аттрибуты модели
 */
abstract class BaseValueField extends BaseField
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $label;
    /**
     * @var mixed
     */
    public $default;
    /**
     * @var bool
     */
    public $required = false;
    /**
     * @var string
     */
    public $helperText;
    /**
     * @var bool
     */
    protected $_isDefaultChanged = false;

    /**
     * @param string $value
     * @return $this
     */
    public function setLabel($value)
    {
        if (is_string($value)) {
            $this->label = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDefault($value)
    {
        $this->_isDefaultChanged = true;
        $this->default = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setRequired($value)
    {
        if (is_bool($value)) {
            $this->required = $value;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function required()
    {
        return $this->setRequired(true);
    }

    /**
     * @param string $helperText
     * @return self
     */
    public function setHelperText($helperText)
    {
        $this->helperText = $helperText;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultValueChanged()
    {
        return $this->_isDefaultChanged;
    }

}
