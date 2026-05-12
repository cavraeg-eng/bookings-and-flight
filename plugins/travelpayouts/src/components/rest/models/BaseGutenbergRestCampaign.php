<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\models;

use Travelpayouts\components\dictionary\Campaigns;
use Travelpayouts\components\InjectedModel;
use Travelpayouts\components\shortcodes\ShortcodeModel;

/**
 * Class BaseRestCampaignModule
 * @package Travelpayouts\components\rest
 * @property-read string|null $name
 */
abstract class BaseGutenbergRestCampaign extends InjectedModel
{
    protected $_shortcodes;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name();
    }

    /**
     * @return string|int
     */
    public function getId()
    {
        return $this->id();
    }

    /**
     * @return ShortcodeModel[]
     * @throws \ReflectionException
     */
    public function getChildren()
    {
        $result = [];
        foreach ($this->attributes() as $attributeName) {
            $frontModelInstance = $this->$attributeName;
            if ($frontModelInstance instanceof ShortcodeModel && $frontModelInstance::isActive()) {
                $result[$attributeName] = $frontModelInstance;
            }
        }
        return $result;
    }

    /**
     * Отдаем данные о входящих в модуль шорткодах
     * @return array
     * @throws \ReflectionException
     */
    public function getShortcodes()
    {
        if (!$this->_shortcodes) {
            $result = [];
            foreach ($this->getChildren() as $model) {
                $result[] = $model->toArray([null], ['id', 'label', 'fields', 'extraData']);
            }
            $this->_shortcodes = $result;
        }

        return $this->_shortcodes;
    }

    /**
     * @inheritdoc
     */
    protected function id()
    {
        return $this->campaignId();
    }

    /**
     * @return int|string
     */
    abstract protected function campaignId();

    protected function name()
    {
        return Campaigns::getInstance()->getItem($this->campaignId())->name;
    }

    public function fields()
    {
        return [
            'id',
            'label' => function () {
                return $this->name();
            },
            'shortcodes',
        ];
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return true;
    }

}
