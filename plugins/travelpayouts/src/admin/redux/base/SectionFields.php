<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\components\section\fields\Accordion;

/**
 * Class SectionFields
 * @package Travelpayouts\admin\redux\base
 * @property-read array|null $fields
 */
abstract class SectionFields extends Base
{
    /**
     * @var Dot
     */
    private $_data;
    /**
     * Выводить секцию в виде аккордеона
     * @var bool
     */
    protected $wrapInAccordion = true;

    /**
     * Помечаем $parent как обязательный для наследников текущего класса
     * @param $parent
     * @param array $config
     */
    public function __construct($parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * Добавляем необходимые префиксы к полям и возвращаем
     * @return array|null
     */
    public function getFields(): ?array
    {
        if (static::isActive()) {
            $label = $this->getLabel();
            $fields = $this->wrapInAccordion && $label ? [
                $this->getAccordionWrapper(),
            ] : $this->fields();

            $resolvedFields = $this->resolveFields($fields, $this->predefinedFields());
            return $resolvedFields ? $this->addPrefixToFields($resolvedFields) : null;
        }
        return null;
    }

    /**
     * Получаем данные из секции
     * @return Dot
     */
    public function getData()
    {
        if (!$this->_data) {
            $this->_data = new Dot($this->getOptionPathData());
        }
        return $this->_data;
    }

    public function setChildren($value): void
    {
        throw new \RuntimeException('Method not implemented');
    }

    public function getLabel(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }

    /**
     * @return Accordion
     */
    protected function getAccordionWrapper(): Accordion
    {
        $label = $this->getLabel();
        $description = $this->getDescription();
        return $this->fieldAccordion()->setTitle($label)->setSubtitle($description)->setFields($this->fields());
    }
}
