<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

use Travelpayouts\components\exceptions\InvalidConfigException;

class AssetEntryInlineVariable extends AssetEntryInlineScript
{
    protected function registerAssetParameters($assetFile): bool
    {
        $name = $this->name;
        $value = $this->processValue($this->value);
        return wp_add_inline_script($assetFile, "var $name = $value;", $this->position);
    }

    protected function processValue($value): string
    {
        if (is_string($value)) {
            // коллбеки не оборачиваем
            return preg_match("/((function\s.*\(.*\)\s)?({(?:[^\{\}]++|(?R))*\}))/", $value) ?
                $value :
                "'$value'";
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return $value;
        }
        throw new InvalidConfigException('invalid value passed');
    }
}
