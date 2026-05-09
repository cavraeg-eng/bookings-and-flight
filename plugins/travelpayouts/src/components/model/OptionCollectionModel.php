<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\model;


/**
 * Class OptionCollectionModel
 * @package Travelpayouts\components\model
 */
abstract class OptionCollectionModel extends CollectionModel
{
    abstract protected function optionPath();

    /**
     * @inheritDoc
     */
    protected function getCollection()
    {
        return json_decode(get_option($this->optionPath(), '[]'), true);
    }

    /**
     * @inheritDoc
     */
    protected function setCollection($value)
    {
        $optionPath = $this->optionPath();
        return get_option($optionPath)
            ? update_option($optionPath, $value)
            : add_option($optionPath, $value);
    }
}
