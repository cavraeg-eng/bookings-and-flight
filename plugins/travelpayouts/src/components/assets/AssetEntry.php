<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

use Travelpayouts\components\BaseObject;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class AssetEntry
 * @package Travelpayouts\components\assets
 * @property-read array $css
 * @property-read array $javascript
 * @property-read array $styleHandlerNameList
 * @property-read array $scriptHandlerNameList
 * @property-read string|null $styleHandlerName
 * @property-read string|null $scriptHandlerName
 */
class AssetEntry extends BaseObject
{
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';

    protected $delimiter = '-';
    /**
     * @var array
     * @see setCss()
     */
    protected $cssFileList = [];
    /**
     * @var array
     * @see setJs()
     */
    protected $javascriptFileList = [];
    protected $cssAssetList;
    protected $javascriptAssetList;
    protected $url = '';
    protected $name;
    protected $prefix = '';
    protected $version = false;
    protected $inFooter = true;

    protected $_isEnqueued = true;

    /**
     * @var BaseAssetEntryParam[]
     */
    protected $_inlineScripts = [];

    protected function setJs($value)
    {
        $this->javascriptFileList = is_array($value)
            ? $value
            : [$value];
    }

    protected function setCss($value)
    {
        $this->cssFileList = is_array($value)
            ? $value
            : [$value];
    }

    public function getCss()
    {
        if (!$this->cssAssetList) {
            $this->cssAssetList = $this->getFileListWithUrls($this->cssFileList);
        }
        return $this->cssAssetList;
    }

    public function getJavascript()
    {
        if (!$this->javascriptAssetList) {
            $this->javascriptAssetList = $this->getFileListWithUrls($this->javascriptFileList);
        }
        return $this->javascriptAssetList;
    }

    protected function getFileListWithUrls($array)
    {
        return array_reduce($array, function ($accumulator, $filePath) {
            $handlerName = implode($this->delimiter, [
                $this->prefix,
                $this->name,
                basename($filePath),
            ]);
            return array_merge($accumulator, [$handlerName => $this->url . basename($filePath)]);
        }, []);
    }

    /**
     * @param array $dependencyList
     * @param bool|null $inFooter
     * @return self
     * @see wp_enqueue_script()
     */
    public function enqueueScript($dependencyList = [], $inFooter = null)
    {
        $version = $this->version;
        if ($inFooter === null) {
            $inFooter = $this->inFooter;
        }

        $assetList = $this->getJavascript();
        foreach ($assetList as $assetHandle => $assetPath) {
            wp_enqueue_script($assetHandle, $assetPath, $dependencyList, $version, $inFooter);
        }
        $this->_isEnqueued = true;
        $this->registerScriptParams();
        return $this;
    }

    /**
     * @param array $dependencyList
     * @param bool|null $inFooter
     * @return self
     * @see wp_register_script()
     */
    public function registerScript($dependencyList = [], $inFooter = null)
    {
        $version = $this->version;
        if ($inFooter === null) {
            $inFooter = $this->inFooter;
        }
        $resolvedDependencies = $this->resolveScriptDependencies($dependencyList);
        $assetList = $this->getJavascript();
        foreach ($assetList as $assetHandle => $assetPath) {
            wp_register_script($assetHandle, $assetPath, $resolvedDependencies, $version, $inFooter);
        }
        return $this;
    }

    /**
     * @param array $dependencyList
     * @param string $media
     * @return self
     * @see wp_enqueue_style()
     */
    public function enqueueStyle($dependencyList = [], $media = 'all')
    {
        $version = $this->version;

        $assetList = $this->getCss();
        foreach ($assetList as $assetHandle => $assetPath) {
            wp_enqueue_style($assetHandle, $assetPath, $dependencyList, $version, $media);
        }
        return $this;
    }

    /**
     * @param array $dependencyList
     * @param string $media
     * @return self
     * @see wp_register_style()
     */
    public function registerStyle($dependencyList = [], $media = 'all')
    {
        $version = $this->version;
        $assetList = $this->getCss();
        foreach ($assetList as $assetHandle => $assetPath) {
            wp_register_style($assetHandle, $assetPath, $dependencyList, $version, $media);
        }
        return $this;
    }

    /**
     * @return self
     * @see wp_deregister_script()
     */
    public function deregisterScript()
    {
        $assetList = $this->getScriptHandlerNameList();
        foreach ($assetList as $assetHandle) {
            wp_deregister_script($assetHandle);
        }
        return $this;

    }

    /**
     * @return self
     * @see wp_dequeue_script()
     */
    public function dequeueScript()
    {
        $assetList = $this->getScriptHandlerNameList();
        foreach ($assetList as $assetHandle) {
            wp_dequeue_script($assetHandle);
        }
        return $this;
    }

    /**
     * @return self
     * @see wp_deregister_style()
     */
    public function deregisterStyle()
    {
        $assetList = $this->getStyleHandlerNameList();
        foreach ($assetList as $assetHandle => $assetPath) {
            wp_deregister_style($assetHandle);
        }
        return $this;
    }

    /**
     * @return self
     * @see wp_dequeue_style()
     */
    public function dequeueStyle()
    {
        $assetList = $this->getStyleHandlerNameList();
        foreach ($assetList as $assetHandle) {
            wp_dequeue_style($assetHandle);
        }
        return $this;
    }

    protected function registerScriptParams()
    {
        foreach ($this->_inlineScripts as $inlineScript) {
           $inlineScript->register();
        }
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     * @see wp_localize_script()
     */
    public function localizeScript($name, $value)
    {
        $this->_inlineScripts = ArrayHelper::addItem($this->_inlineScripts, new AssetEntryLocalizeVariable(
            [
                'asset' => $this,
                'name' => $name,
                'value' => $value,
            ]));

        $this->registerScriptParams();
        return $this;
    }

    /**
     * @param string $data String containing the javascript to be added.
     * @param string $position Optional. Whether to add the inline script before the handle
     * @return $this
     * @see wp_add_inline_script()
     */
    public function addInlineScript($data, $position = self::POSITION_AFTER)
    {
        $this->_inlineScripts = ArrayHelper::addItem($this->_inlineScripts, new AssetEntryInlineScript(
            [
                'asset' => $this,
                'value' => $data,
                'position' => $position,
            ]
        ));

        $this->registerScriptParams();
        return $this;
    }

    /**
     * @param string $name Name of the variable
     * @param string|number|bool|array $data String containing the javascript to be added.
     * @param string $position Optional. Whether to add the inline script before the handle
     * @return $this
     * @see wp_add_inline_script()
     */
    public function addInlineVariable($name, $data, $position = self::POSITION_AFTER)
    {
        $this->_inlineScripts = ArrayHelper::addItem($this->_inlineScripts, new AssetEntryInlineVariable(
            [
                'asset' => $this,
                'name' => $name,
                'value' => $data,
                'position' => $position,
            ]
        ));
        $this->registerScriptParams();

        return $this;
    }

    /**
     * Возвращаем все имеющиеся имена хендлеров
     * @return array
     */
    public function getStyleHandlerNameList()
    {
        return array_keys($this->getCss());
    }

    /**
     * Возвращаем все имеющиеся имена хендлеров
     * @return array
     */
    public function getScriptHandlerNameList()
    {
        return array_keys($this->getJavascript());
    }

    /**
     * Возвращаем первый имеющийся хендлер, если имеется
     * @return string|null
     */
    public function getStyleHandlerName()
    {
        return ArrayHelper::getFirst($this->getStyleHandlerNameList());
    }

    /**
     * Возвращаем первый имеющийся хендлер, если имеется
     * @return string|null
     */
    public function getScriptHandlerName()
    {
        return ArrayHelper::getFirst($this->getScriptHandlerNameList());
    }

    protected function resolveScriptDependencies($dependencyList)
    {
        return $this->findInternalDependencies($this->getRegisteredScripts(), $dependencyList);
    }

    protected function findInternalDependencies($source, $dependencyList = [])
    {
        if (!empty($dependencyList)) {
            return array_map(function ($dependencyName) use ($source) {
                $assetDependencyName = $this->prefix . $this->delimiter . $dependencyName;
                $foundAssetDependency = ArrayHelper::find($source, static function ($name) use ($assetDependencyName) {
                    return strpos($name, $assetDependencyName) !== false;
                });
                return $foundAssetDependency !== null
                    ? $foundAssetDependency
                    : $dependencyName;
            }, $dependencyList);
        }

        return $dependencyList;
    }

    protected function getRegisteredScripts()
    {
        return array_keys(wp_scripts()->registered);
    }

    protected function getRegisteredStyles()
    {
        return array_keys(wp_scripts()->registered);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setVersion($value)
    {
        if (is_string($this->version) || is_bool($value)) {
            $this->version = $value;
        }
        return $this;
    }

    public function setInFooter($value)
    {
        if (is_string($this->inFooter) || is_bool($value)) {
            $this->inFooter = $value;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnqueued(): bool
    {
        return $this->_isEnqueued;
    }

}
