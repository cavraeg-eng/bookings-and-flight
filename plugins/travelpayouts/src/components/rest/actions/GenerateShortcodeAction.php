<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\actions;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\ShortcodesTagHelper;
use Travelpayouts\components\web\CheckAccessAction;
use Travelpayouts\components\web\Request;
use Travelpayouts\helpers\ArrayHelper;

class GenerateShortcodeAction extends CheckAccessAction
{
    protected const STATUS_VALIDATION_ERROR = 'invalid_value';

    /**
     * @var callable
     */
    public $checkAccess;
    public $modelClass;

    public function run()
    {
        $attributes = Request::getInstance()->getInputData();
        $model = $this->getModel();
        $model->attributes = $attributes;
        if (!is_array($attributes) || !$model->validate()) {
            $this->response->setStatusCode(400, 'model validation error');
            return [
                'data' => ['error' => $model->getErrors()],
                'meta' => [
                    'status' => self::STATUS_VALIDATION_ERROR,
                ],
            ];
        }

        $shortcodeTag = ArrayHelper::getFirst($model::shortcodeTags());
        if (!is_string($shortcodeTag)) {
            throw new InvalidConfigException('shortcodeTags must contain only strings');
        }

        return [
            'shortcode' => $this->generateShortcode($shortcodeTag, $model->toArray(array_keys($attributes))),
        ];
    }

    /**
     * Приводим аттрибуты в строке
     * @param mixed[]
     * @return string[]
     */
    protected function normalizeFields($fields)
    {
        $result = [];
        foreach ($fields as $name => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $result[strtolower($name)] = (string)$value;
        }
        return $result;
    }

    protected function filterFields($data)
    {
        return array_filter($data, static function ($value) {
            if ($value === null || ($value === '')) {
                return false;
            }
            if (is_string($value) || is_bool($value) || is_numeric($value)) {
                return true;
            }
            return false;
        });
    }

    protected function generateShortcode($shortcodeTag, $attributes)
    {
        $shortcodeAttributes = $this->filterFields($this->normalizeFields($attributes));
        return ShortcodesTagHelper::selfClosing($shortcodeTag, $shortcodeAttributes);
    }

    /**
     * @return ShortcodeModel
     * @throws InvalidConfigException
     */
    protected function getModel()
    {
        /** @var ShortcodeModel $model */
        $model = new $this->modelClass();
        $model->set_scenario(ShortcodeModel::SCENARIO_GENERATE_SHORTCODE);
        if (!$model instanceof ShortcodeModel) {
            throw new InvalidConfigException('modelClass must be instance of ShortcodeModel');
        }
        return $model;
    }
}
