<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

use RuntimeException;
use Travelpayouts;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class WebpackAssets
 * @package Travelpayouts\src\includes
 * @property-read array $assets
 * @property-read string $assetsPath
 * @property-read string $assetsUrl
 */
abstract class WebpackAssets extends BaseInjectedObject
{
    protected $_assets;
    /**
     * @var AssetEntry[]
     */
    protected $_assetsEntries = [];

    /**
     * @return string
     */
    abstract protected function assetsPath();

    /**
     * @return string
     */
    abstract public function prefix();

    protected function getAssetsPath()
    {
        return $this->assetsPath();
    }

    protected function getAssetsUrl()
    {
        return plugin_dir_url($this->assetsPath);
    }

    protected function getAssets()
    {
        if (!$this->_assets) {
            $pathInfo = pathinfo($this->assetsPath);
            if ($pathInfo['extension'] === 'json' && file_exists($this->assetsPath)) {
                $assetsFile = json_decode(file_get_contents($this->assetsPath), true);
                if ($assetsFile) {
                    $this->_assets = $assetsFile;
                }
            }
        }
        return $this->_assets;
    }

    /**
     * @param $name
     * @return AssetEntry
     */
    public function getAssetByName($name)
    {
        if ($this->assets) {
            $assetsEntries = $this->getAssetsEntries();
            $assetEntry = ArrayHelper::find($assetsEntries, static function ($assetName) use ($name) {
                return $assetName === $name;
            }, ARRAY_FILTER_USE_KEY);
            if ($assetEntry) {
                return $assetEntry;
            }
        }
        throw new RuntimeException(Travelpayouts::__('Chunk with name "{name}" not found in "{path}"', [
            'name' => $name,
            'path' => $this->getAssetsPath(),
        ]));
    }

    protected function getAssetsEntries()
    {
        if (empty($this->_assetsEntries)) {
            $assetList = $this->getAssets();
            $this->_assetsEntries = array_reduce(array_keys($this->getAssets()), function ($accumulator, $assetName) use ($assetList) {
                if (isset($assetList[$assetName])) {
                    $assetContent = array_merge($assetList[$assetName], [
                        'url' => $this->getAssetsUrl(),
                        'name' => $assetName,
                        'prefix' => $this->prefix(),
                        'version' => $this->getAssetVersion(),
                    ]);
                    return array_merge($accumulator, [$assetName => new AssetEntry($assetContent)]);
                }
                return $accumulator;
            }, []);
        }
        return $this->_assetsEntries;
    }

    protected function getAssetVersion()
    {
        return TRAVELPAYOUTS_VERSION;
    }
}
