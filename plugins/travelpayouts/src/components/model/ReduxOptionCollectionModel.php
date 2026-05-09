<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\model;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\includes\ReduxConfigurator;

/**
 * Class ReduxOptionCollectionModel
 * @package Travelpayouts\components\model
 */
abstract class ReduxOptionCollectionModel extends OptionCollectionModel
{
    /**
     * @Inject
     * @var ReduxConfigurator
     */
    protected $redux;

    protected function getCollection()
    {
        $data = $this->redux->getOption($this->optionPath(), '[]');
        if (!is_null($data) && $data !== '') {
            return json_decode($data, true);
        }

        return [];
    }

    protected function setCollection($value)
    {
        return $this->redux->setOption($this->optionPath(), $value);
    }
}
