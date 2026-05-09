<?php

/**
 * The public-facing functionality of the plugin.
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/public
 */

namespace Travelpayouts\frontend;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\Assets;
use Travelpayouts\components\assets\AssetEntry;
use Travelpayouts\components\brands\PlatformsEndpoint;
use Travelpayouts\components\HookableObject;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\controllers\LinksController;
use Travelpayouts\includes\HooksLoader;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\moneyScript\components\MoneyScript;
use Travelpayouts\modules\settings\SettingsForm;
use Travelpayouts\admin\components\AirtableDistribution;
use Travelpayouts\modules\tables\components\settings\CustomTableStylesSection;
use Travelpayouts\modules\tables\components\settings\FlightsSettingsSection;
use Travelpayouts\modules\tables\components\settings\HotelSettingsSection;
use Travelpayouts\Vendor\League\Plates\Engine;

/**
 * The public-facing functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * @package    Travelpayouts
 * @subpackage Travelpayouts/public
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 */
class PublicHooks extends HookableObject
{
    /**
     * @var CustomTableStylesSection
     * @Inject
     */
    protected $customTableStyles;

    /**
     * @var FlightsSettingsSection
     * @Inject
     */
    protected $flightsSettings;

    /**
     * @var HotelSettingsSection
     * @Inject
     */
    protected $hotelsSettings;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $settingsSection;

    /**
     * @var MoneyScript
     * @Inject
     */
    protected $moneyScript;

    /**
     * @var AirtableDistribution
     * @Inject
     */
    protected $airtableDistribution;

    /**
     * @Inject
     * @var Engine
     */
    public $template;

    /**
     * @Inject
     * @var Account
     */
    public $account;

    public function init()
    {
        // Регистрируем контроллер
        new LinksController();
    }

    public function hookList(HooksLoader $hooksLoader)
    {
        $hooksLoader
            ->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts'], 100)
            ->addFilter('template_redirect', [UrlHelper::getInstance(), 'externalRedirectAction'])
            ->addFilter('allowed_redirect_hosts', [$this, 'allowedRedirectHosts'])
            ->addAction('wp_footer', [$this->moneyScript, 'run']);

        $hooksLoader->addAction('wp_head', [$this, 'appendCustomTableStyles']);

        if ($this->airtableDistribution->shouldAddScript()) {
            $hooksLoader->addAction('wp_head', [$this, 'addAnalyticsScript']);
        }

        $hooksLoader->addAction('wp_head', [$this, 'addPlatformsScript']);
    }

    public function allowedRedirectHosts($hosts)
    {
        $allowedHosts = [
            'c45.travelpayouts.com',
            'tp.media'
        ];

        return array_merge($hosts, $allowedHosts);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @since    1.0.0
     */
    public function enqueueScripts()
    {
        $this->getAssets()->loader->registerAsset('public-scripts');
        $this->getRuntimeAsset()
            ->addInlineVariable('travelpayoutsAjaxEndpoint', admin_url('admin-ajax.php'))
            ->addInlineVariable('travelpayoutsUseFilterRef', $this->settingsSection->getUseFilterRef());
        $this->addTableEvents();

        /**
         * TODO проверить если jquery не registered или wp_deregister_script или подменен другим
         */
        if (wp_script_is('jquery', 'registered')) {
            wp_enqueue_script('jquery');
        }
    }

    public function appendCustomTableStyles()
    {
        $stylesheets = $this->customTableStyles->getStylesheets();
        if ($stylesheets) {
            echo HtmlHelper::tag('style', ['type' => 'text/css'], $stylesheets, true);
        }
    }

    private function addTableEvents()
    {
        $settings = $this->settingsSection;
        $loadEvent = !empty($settings->table_load_event) ? $settings->table_load_event : 'return true;';
        $buttonEvent = !empty($settings->table_btn_event) ? $settings->table_btn_event : 'return true;';
        $this->getRuntimeAsset()
            ->addInlineVariable('travelpayoutsOnTableLoadEvent', "function (){ $loadEvent }")
            ->addInlineVariable('travelpayoutsOnTableBtnClickEvent', "function (){ $buttonEvent }");
    }


    /**
     * @return Assets
     */
    protected function getAssets(): Assets
    {
        return Travelpayouts::getInstance()->assets;
    }

    protected function getRuntimeAsset(): AssetEntry
    {
        return $this->getAssets()->getAssetByName('runtime');
    }

    public function addAnalyticsScript()
    {
        echo $this->template->render('admin::script', [
            'marker' => $this->account->marker
        ]);
    }

    public function addPlatformsScript()
    {
        $platforms = PlatformsEndpoint::getInstance();

        $scriptLink = $platforms->getScriptLink();

        if (!empty($scriptLink)) {
            echo $this->template->render('admin::platformsScript', [
                'scriptLink' => $scriptLink
            ]);
        }

    }
}
