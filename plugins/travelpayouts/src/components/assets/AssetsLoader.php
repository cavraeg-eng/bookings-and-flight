<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\assets;

use Travelpayouts;
use Travelpayouts\components\Assets;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\includes\HooksLoader;

class AssetsLoader extends Travelpayouts\components\HookableObject
{
    public $loadableChunkName = 'loadable_chunks';

    protected $_assetList = [];

    /**
     * @var Assets
     */
    protected $assets;

    /**
     * @inheritDoc
     */
    protected function hookList(HooksLoader $hooksLoader)
    {
        $hooksLoader
            ->addAction('wp_footer', [$this, 'renderLoadableChunkList'], 1000)
            ->addAction('admin_footer', [$this, 'renderLoadableChunkList'], 1000)
            ->addAction('wp_enqueue_scripts', [$this, 'enqueueLoader'], 1000)
            ->addAction('admin_enqueue_scripts', [$this, 'enqueueLoader'], 1000)
            ->addFilter('litespeed_optimize_js_excludes', [$this, 'optimizerExclusionsLiteSpeed'])
            ->addFilter('litespeed_optimize_css_excludes', [$this, 'optimizerExclusionsLiteSpeed'])
            ->addFilter('litespeed_optm_js_defer_exc', [$this, 'optimizerExclusionsLiteSpeed'])
            ->addFilter('rocket_exclude_js', [$this, 'optimizerExclusionsRocketWp'])
            ->addFilter('rocket_delay_js_exclusions', [$this, 'optimizerExclusionsRocketWp'])
            ->addFilter('rocket_exclude_defer_js', [$this, 'optimizerExclusionsRocketWp']);
    }

    public function enqueueLoader()
    {
        $this->assets->getAssetByName('runtime')
            ->setInFooter(true)
            ->enqueueScript()
            ->addInlineVariable('travelpayouts_plugin_publicPath', Travelpayouts::getAlias('@webroot/assets') . '/');
        $this->assets->getAssetByName('loader')
            ->setInFooter(true)
            ->enqueueScript()
            ->enqueueStyle();
    }

    public function renderLoadableChunkList()
    {
        $assetList = array_values(array_unique($this->_assetList));
        echo HtmlHelper::script('var ' . $this->loadableChunkName . ' = ' . json_encode($assetList) . ';', [
            'data-optimized' => '1',
            'data-wp-rocket-ignore' => '1',
        ]);
    }

    /**
     * Подключаем скрипты и стили
     * Список доступных ассетов находится тут src/js/src/loader/chunks
     * @param string $name
     * @return $this
     */
    public function registerAsset($name)
    {
        if (is_string($name)) {
            $this->_assetList = array_merge($this->_assetList, [$name]);
        }
        return $this;
    }

    /**
     * @param string[] $assetNameList
     * @return $this
     * @see AssetsLoader::registerAsset()
     */
    public function registerAssets($assetNameList)
    {
        if (is_array($assetNameList)) {
            $this->_assetList = array_merge($this->_assetList, $assetNameList);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function renderPreloader()
    {
        return HtmlHelper::tagArrayContent('div', ['class' => 'travelpayouts-chunk-preloader-wrapper'], [
            HtmlHelper::tagArrayContent('div', ['class' => 'travelpayouts-chunk-preloader'], [
                HtmlHelper::tag('div', [], ''),
                HtmlHelper::tag('div', [], ''),
                HtmlHelper::tag('div', [], ''),
                HtmlHelper::tag('div', [], ''),
            ]),
        ]);
    }

    public function optimizerExclusionsLiteSpeed($values)
    {
        $assetsUrl = plugins_url('', $this->assets->assetsPath);
        return is_array($values) ? ArrayHelper::addItem($values, $assetsUrl) : [$assetsUrl];
    }

    public function optimizerExclusionsRocketWp($values)
    {
        $excludeAttributes = [
            'travelpayouts/assets',
            'data-wp-rocket-ignore',
            'jquery',
        ];

        return is_array($values) ? array_merge($values, $excludeAttributes) : $excludeAttributes;
    }
}
