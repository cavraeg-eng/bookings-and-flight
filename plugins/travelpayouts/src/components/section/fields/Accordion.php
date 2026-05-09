<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\section\fields;

use Travelpayouts\admin\redux\extensions\OscAccordionField;
use Travelpayouts\components\section\ReduxFieldResolverTrait;

class Accordion extends BaseField
{
    use ReduxFieldResolverTrait;

    public $type = OscAccordionField::TYPE;
    public $open = false;

    public $skipSave = true;

    protected $_fields = [];

    protected $_predefinedFields = [];

    public $title;

    public function setPredefinedFields(array $fields)
    {
        $this->_predefinedFields = $fields;
        return $this;
    }

    public function setFields($fields = [])
    {
        if (is_array($fields)) {
            $this->_fields = $fields;
        }
        return $this;

    }

    public function result(): array
    {
        $id = md5(mt_rand());
        $fields = array_merge(
            [
                (new AccordionOpen())
                    ->setID("{$id}_open")
                    ->setTitle($this->title)
                    ->setSubtitle($this->subtitle)
                    ->setIsOpen($this->open),
            ],
            $this->_fields,
            [
                (new AccordionClose())
                    ->setID("{$id}_close"),
            ]);
        return $this->resolveFields($fields, $this->_predefinedFields);
    }

    /**
     * @param bool $open
     * @return self
     */
    public function setIsOpen(bool $open): self
    {
        $this->open = $open;
        return $this;
    }

}
