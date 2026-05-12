<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\models;

use Travelpayouts\components\Model;

/**
 * Class BaseRestModule
 * @package Travelpayouts\components\rest
 */
abstract class BaseGutenbergRestModule extends Model
{
    private $_campaignList = [];

    /**
     * Список классов кампаний входящих в модуль
     * @return string[]
     */
    abstract protected function campaignList();

    /**
     * @return string
     */
    abstract protected function id();

    /**
     * @return string
     */
    abstract protected function title();

    /**
     * @return BaseGutenbergRestCampaign[]
     */
    protected function getCampaigns()
    {
        if (empty($this->_campaignList)) {
            $result = [];
            foreach ($this->campaignList() as $className) {
                if (is_a($className, BaseGutenbergRestCampaign::class, true)) {
                    $campaignInstance = new $className;
                    if ($campaignInstance->isActive()) {
                        $result[] = $campaignInstance;
                    }
                }
            }
            $this->_campaignList = $result;
        }

        return $this->_campaignList;
    }

    /**
     * Массив с дополнительными данными для эндпоинта
     * @return array[]
     */
    protected function getExtraData()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->id();
    }

    public function fields()
    {
        return [
            'id' => function () {
                return $this->id();
            },
            'label' => function () {
                return $this->title();
            },
            'campaigns',
            'extraData',
        ];
    }
}
