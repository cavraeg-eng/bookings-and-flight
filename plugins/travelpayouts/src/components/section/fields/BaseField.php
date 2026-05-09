<?php

namespace Travelpayouts\components\section\fields;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\Model;

class BaseField extends Model
{
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $subtitle;
    /**
     * @var string
     */
    public $desc;
    /**
     * @var string
     */
    public $class;
    /**
     * @var array
     */
    public $required;
    /**
     * @var bool
     */
    public $default = false;

    /**
     * Скрывать заголовок поля
     * @var bool
     */
    public $hideTitle = false;

    /**
     * Не сохранять значение поля в базу данных
     * @var bool
     */
    public $skipSave = false;

    /**
     * Оборачивание поля в fieldset
     * @var bool
     */
    public $wrapField = true;

    /**
     * @var bool
     */
    public $hidden = false;

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('Type attribute must be set');
        }
    }

    /**
     * @param $id
     * @return $this
     */
    public function setID($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        if (is_string($value)) {
            $this->title = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSubtitle($value)
    {
        if (is_string($value)) {
            $this->subtitle = $value;
        }

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDesc(string $value)
    {
        if (is_string($value)) {
            $this->desc = $value;
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDefault($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setClass($value)
    {
        if (is_string($value)) {
            $this->class = $value;
        }

        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setRequired($value)
    {
        if (is_array($value)) {
            $this->required = $value;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return self
     */
    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }



    public function fields()
    {
        return $this->attributes();
    }

    public function result(): array
    {
        if ($this->hidden) {
            return [];
        }

        return array_filter($this->toArray(), function ($value) {
            return !is_null($value);
        });
    }
}