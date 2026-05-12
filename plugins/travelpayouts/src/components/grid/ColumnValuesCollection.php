<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid;

class ColumnValuesCollection
{
    protected $_values = [];

    public function addValue($model, $key, $index, $value)
    {
        $key = $this->getKey($model, $key, $index);
        $this->_values[$key] = $value;
    }

    public function getValue($model, $key, $index)
    {
        $key = $this->getKey($model, $key, $index);
        return $this->_values[$key] ?? null;
    }

    protected function getKey($model, $key, $index): string
    {
        return md5(get_class($model) . '_' . $key . '_' . $index);
    }

}
