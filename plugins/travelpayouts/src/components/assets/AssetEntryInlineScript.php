<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

class AssetEntryInlineScript extends BaseAssetEntryParam
{
    public $position;
    public $value;

    protected function registerAssetParameters($assetFile): bool
    {
        return wp_add_inline_script($assetFile, $this->value, $this->position);
    }
}
