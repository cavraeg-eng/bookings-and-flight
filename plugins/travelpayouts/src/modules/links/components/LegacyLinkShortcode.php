<?php

namespace Travelpayouts\modules\links\components;

use Travelpayouts;
use Travelpayouts\components\ErrorHelper;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\modules\links\components;

/**
 * Class LegacyLinkShortcode
 * Устаревший класс для шорткода tp_link, для поддержания обратной совместимости
 * @package Travelpayouts\src\modules\links
 */
class LegacyLinkShortcode extends ShortcodeModel
{
    const TYPE_FLIGHTS = 1;
    const TYPE_HOTELS = 2;

    /**
     * @var string
     */
    public $tag;

    /**
     * @var int
     */
    public $type;
    /**
     * @var BaseLinkShortcode
     */
    protected $_model;

    /**
     * @var array
     */
    public $shortcodeAttributes = [];

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type'], 'required'],
            [
                ['type'],
                'in',
                'range' => [
                    self::TYPE_FLIGHTS,
                    self::TYPE_HOTELS,
                ],
                'message' => Travelpayouts::__('Please select valid link type'),
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return [
            'tp_link',
        ];
    }

    public function render()
    {
        $model = $this->getShortcodeModel();
        if ($model) {
            $model->attributes = $this->shortcodeAttributes;
            if (!$model->validate()) {
                return ErrorHelper::render_errors($this->tag, $model->getErrors());
            }
            return $model->render();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new static();
        $model->tag = $tag;
        $model->attributes = $attributes;
        $model->shortcodeAttributes = $attributes;
        if (!$model->validate()) {
            return ErrorHelper::render_errors($tag, $model->getErrors());
        }
        return $model->render();
    }

    /**
     * @return BaseLinkShortcode| null
     */
    public function getShortcodeModel()
    {
        if (!$this->_model) {
            switch ($this->type) {
                case self::TYPE_FLIGHTS:
                    $this->_model = new components\flights\Shortcode();
                    break;
                case self::TYPE_HOTELS:
                    $this->_model = new components\hotels\Shortcode();
                    break;
                default:
                    return null;
            }
        }
        return $this->_model;
    }
}
