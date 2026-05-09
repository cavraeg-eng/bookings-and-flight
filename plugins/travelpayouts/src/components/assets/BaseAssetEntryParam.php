<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

use Travelpayouts\components\BaseObject;
use Travelpayouts\components\exceptions\InvalidConfigException;

abstract class BaseAssetEntryParam extends BaseObject
{
    /**
     * @var AssetEntry
     */
    public $asset;
    /**
     * @var string|null
     */
    public $name;

    public $isRegistered = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->asset) {
            throw new InvalidConfigException('it seems that you forgot to pass asset property');
        }
    }

    abstract protected function registerAssetParameters($assetFile): bool;

    public function register(): void
    {
        if (!$this->isRegistered && $this->asset->getIsEnqueued()) {
            $scriptHandler = $this->asset->getScriptHandlerName();
            if ($this->registerAssetParameters($scriptHandler)) {
                $this->isRegistered = true;
            }
        }
    }
}
