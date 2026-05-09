<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\base\dictionary;
use Travelpayouts\Vendor\Adbar\Dot;
use Exception;
use Travelpayouts\components\BaseObject;
use Travelpayouts\traits\GetterSetterTrait;

/**
 * Class Item
 * @package Travelpayouts\src\components\dictionary\items
 * @property-read string $name
 * @property-read array $data
 * @property-read Dot $dataDot
 */
class Item extends BaseObject
{
    use GetterSetterTrait {
        __get as traitGet;
    }

    protected $_data;
    protected $_dataDot;
    protected $_lang;

    public function init()
    {
        if (!$this->_data || !is_array($this->_data)) {
            throw new Exception("[{$this->getClassName()}]: \$data must be an array");
        }

        $this->setDataDot($this->_data);
    }

    public function __get($name)
    {
        $attributeData = $this->_dataDot->get($name);
        if ($attributeData)
            return $attributeData;
        return $this->traitGet($name);
    }

    public function get_data()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return static::class;
    }

    protected function setDataDot($value)
    {
        if (is_array($value)) {
            $this->_dataDot = new Dot($value);
        }
    }

    protected function getDataDot()
    {
        return $this->_dataDot;
    }
}
