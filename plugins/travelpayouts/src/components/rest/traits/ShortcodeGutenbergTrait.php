<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\traits;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\rest\fields\BaseField;
use Travelpayouts\components\rest\fields\BaseValueField;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\validators\ValidatorRequired;
use Travelpayouts\helpers\ArrayHelper;

trait ShortcodeGutenbergTrait
{
    /**
     * Список полей для гутенберга
     * @return array
     */
    abstract public function gutenbergFields(): array;

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getGutenbergFields(): array
    {
        return ArrayHelper::toArray($this->resolveGutenbergFieldList($this->gutenbergFields()));
    }

    /**
     * Предустановленные поля для гутенберга
     * @return array
     */
    protected function predefinedGutenbergFields(): array
    {
        return [];
    }

    /**
     * Получаем поля по имени
     * @param $name
     * @return BaseField|null
     */
    protected function getPredefinedFieldByName($name): ?BaseField
    {
        $fields = $this->predefinedGutenbergFields();
        return isset($fields[$name]) ? clone $fields[$name] : null;
    }

    /**
     * Получаем список моделей полей
     * @param array $fieldList
     * @return BaseField[]
     * @throws InvalidConfigException
     */
    protected function resolveGutenbergFieldList(array $fieldList = []): array
    {
        $result = [];
        foreach ($fieldList as $id => $field) {
            if (is_string($field)) {
                $id = $field;
                $field = $this->getPredefinedFieldByName($id);
            }
            if (!$field instanceof BaseField) {
                throw new InvalidConfigException(get_class($this) . ' Field property must be an instance of BaseField');
            }
            $this->set_scenario(ShortcodeModel::SCENARIO_GENERATE_SHORTCODE);
            if ($field instanceof BaseValueField) {
                $activeValidators = $this->get_active_validators($id);
                // Проставляем значение required на основании валидаторов
                $requiredValidatorInList = ArrayHelper::find($activeValidators, static function ($validator) {
                    return $validator instanceof ValidatorRequired;
                });
                if ($requiredValidatorInList) {
                    $field->setRequired(true);
                }
                if (!is_string($id)) {
                    throw new InvalidConfigException(get_class($this) . ' Id property must be string');
                }
                // Проставляем остальные необходимые аттрибуты на основе данных модели
                $field->id = $id;

                if (!$field->isDefaultValueChanged() && $this->is_attribute_safe($id) && $this->{$id} !== null) {
                    $field->default = $this->{$id};
                }
                if (!$field->label) {
                    $field->setLabel($this->get_attribute_label($id));
                }
            }
            $result[] = $field;
        }
        return $result;
    }

}