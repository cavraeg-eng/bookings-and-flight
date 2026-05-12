<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\section;

use Travelpayouts\components\section\fields\BaseField;
use Travelpayouts\helpers\ArrayHelper;

trait ReduxFieldResolverTrait
{

    protected function resolveFields(array $fields, $predefinedFields = []): array
    {
        $resolvedFields = [];
        foreach ($fields as $key => $field) {
            $fieldData = $this->resolveField($field, $key, $predefinedFields);
            if ($fieldData) {
                if (ArrayHelper::isIndexed($fieldData)) {
                    $resolvedFields = array_merge($resolvedFields, $fieldData);
                } else {
                    $resolvedFields[] = $fieldData;
                }
            }
        }
        return $resolvedFields;
    }

    protected function resolveField($field, $name, $predefinedFields = []): ?array
    {
        if ($field instanceof BaseField) {
            /** @var BaseField $value */
            if (is_string($name)) {
                $field->setID($name);
            }
            $fieldDataAsArray = $field->result();
            return !empty($fieldDataAsArray) ? $fieldDataAsArray : null;
        }

        if (is_array($field) && isset($field['id'])) {
            return $field;
        }

        if (is_string($field)) {
            $resolvedField = $this->getPredefinedFieldByName($field, $predefinedFields);
            return $this->resolveField($resolvedField, $name);
        }

        return null;
    }

    /**
     * @param string $name
     * @param array $predefinedFields
     * @return BaseField
     */
    protected function getPredefinedFieldByName(string $name, array $predefinedFields = []): ?BaseField
    {
        if (isset($predefinedFields[$name]) && $predefinedFields[$name] instanceof BaseField) {
            return clone $predefinedFields[$name];
        }

        return null;
    }

}