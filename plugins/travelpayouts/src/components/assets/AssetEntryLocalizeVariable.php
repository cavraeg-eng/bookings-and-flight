<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

class AssetEntryLocalizeVariable extends BaseAssetEntryParam
{
    public $value;

    protected function registerAssetParameters($assetFile): bool
    {
        return wp_localize_script($assetFile, $this->name, $this->value);
    }
}
