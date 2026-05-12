<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use RuntimeException;
use Travelpayouts\components\Assets;
use Travelpayouts\components\Logger;
use Travelpayouts\components\HookableObject;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\rest\controllers\GutenbergRestController;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\includes\HooksLoader;
use Travelpayouts\traits\SingletonTrait;

class EditorHooks extends HookableObject
{
    use SingletonTrait;

    /**
     * @Inject("name")
     */
    protected $pluginName;
    /**
     * @Inject
     * @var Logger
     */
    protected $logger;
    /**
     * @Inject
     * @var Assets
     */
    protected $assets;

    /**
     * @Inject("snowplow.context")
     * @var array
     */
    protected $snowPlowContext;

    /**
     * @Inject
     * @var HooksLoader
     */
    protected $hooksLoader;

    public function hookList(HooksLoader $hooksLoader)
    {
        $hooksLoader
            ->addAction('admin_enqueue_scripts', [$this, 'localizeScripts'])
            ->addFilter('mce_external_plugins', [$this, 'addTinyMcePlugin'])
            ->addFilter('mce_buttons', [$this, 'addTinyMceButton'])
            ->addAction('init', [$this, 'setUpGutenbergAssetSettings'])
            ->addAction('init', [$this, 'loadGutenbergBlock'])
//          @TODO раскомментировать после окончания работ над интеграцией elementor
//          ->registerHooksInstance(new \Travelpayouts\admin\components\elementor\ElementorHooks())
        ;

        if (function_exists('wp_enqueue_editor')) {
            $hooksLoader->addAction('wp_enqueue_editor', [$this, 'enqueueBlockEditorAssets']);
        } else {
            $hooksLoader->addAction('admin_enqueue_scripts', [$this, 'enqueueBlockEditorAssets']);
        }
    }

    public function setUpGutenbergAssetSettings()
    {
        $gutenbergRestController = new GutenbergRestController();
        $this->assets->getAssetByName('admin-gutenberg-modal')
            ->localizeScript($this->pluginName . 'FrontApiSettings', [
                'nonce' => wp_create_nonce('wp_rest'),
                'endpoint' => esc_url(rest_url($gutenbergRestController->routerPath)),
                'locale' => LanguageHelper::dashboardLocale(),
                'settings_locale' => LanguageHelper::tableLocale(),
            ]);
    }

    /**
     * Add <script> tag with JavaScript object
     * @since    1.0.0
     */
    public function localizeScripts()
    {
        wp_localize_script('jquery',
            $this->pluginName . 'Data',
            [
                'context' => $this->snowPlowContext,
            ]
        );
    }

    public function loadGutenbergBlock()
    {

        try {
            $gutenbergAsset = $this
                ->assets
                ->getAssetByName('admin-gutenberg-injector')
                ->registerStyle(['wp-editor'], null)
                ->registerScript([
                    'wp-blocks',
                    'wp-i18n',
                    'wp-element',
                    'wp-editor',
                ],
                    true
                )->localizeScript(
                    $this->pluginName . '_block_shortcodes_globals', // Array containing dynamic data for a JS Global.
                    [
                        'pluginDirPath' => plugin_dir_path(__DIR__),
                        'pluginDirUrl' => plugin_dir_url(__DIR__),
                    ]
                );

            /**
             * Register Gutenberg block on server-side.
             * Register the block on server-side to ensure that the block
             * scripts and styles for both frontend and backend are
             * enqueued when the editor loads.
             * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
             * @since 1.16.0
             */
            if (function_exists('register_block_type')) {
                register_block_type(
                    $this->pluginName . '/shortcodes', [
                        'editor_style' => $gutenbergAsset->styleHandlerName,
                        'editor_script' => $gutenbergAsset->scriptHandlerName,
                    ]
                );
            }
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * @param $buttons
     * @return array
     */
    public function addTinyMceButton($buttons)
    {
        return array_merge($buttons, [$this->pluginName . '_shortcodes_btn']);
    }

    public function addTinyMcePlugin($pluginList)
    {
        $pluginName = $this->pluginName . '_shortcodes_editor_btn';

        $assetList = $this
            ->assets
            ->getAssetByName('admin-classic-editor-injector')->getJavascript();

        if ($asset = ArrayHelper::getFirst($assetList)) {
            $pluginList[$pluginName] = $asset;
        }

        return $pluginList;
    }

    public function enqueueBlockEditorAssets()
    {
        try {
            $this->assets->getAssetByName('admin-gutenberg-modal')
                ->setInFooter(true)->enqueueStyle()->enqueueScript();
        } catch (RuntimeException $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
