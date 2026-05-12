<?php

/**
 * The file that defines the core plugin class
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 */
use Travelpayouts\Vendor\League\Plates\Engine;
use Travelpayouts\admin\AdminHooks;
use Travelpayouts\components\Assets;
use Travelpayouts\components\base\BasePluginCore;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\Logger;
use Travelpayouts\components\exceptions\TravelpayoutsException;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\Module;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\components\multilingual\MultiLang;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\components\Rights;
use Travelpayouts\components\snowplow\Tracker;
use Travelpayouts\components\Translator;
use Travelpayouts\components\web\ExceptionRenderer;
use Travelpayouts\components\web\Request;
use Travelpayouts\frontend\PublicHooks;
use Travelpayouts\includes\Deactivator;
use Travelpayouts\includes\HooksLoader;
use Travelpayouts\includes\I18n;
use Travelpayouts\includes\ReduxConfigurator;
use Travelpayouts\includes\Router;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\help\HelpModule;
use Travelpayouts\modules\links\Links;
use Travelpayouts\modules\moneyScript\MoneyScriptModule;
use Travelpayouts\modules\searchForms\SearchFormModule;
use Travelpayouts\modules\settings\Settings;
use Travelpayouts\modules\tables\Tables;
use Travelpayouts\modules\widgets\Widgets;

/**
 * The core plugin class.
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 * @property-read ModuleRedux[] $reduxModules
 */
class Travelpayouts extends BasePluginCore
{

    /**
     * @Inject
     * @var Settings
     */
    public $settings;

    /**
     * @Inject
     * @var Account
     */
    public $account;

    /**
     * @Inject
     * @var Cache
     */
    public $cache;

    /**
     * @Inject
     * @var Translator
     */
    public $translator;

    /**
     * @Inject
     * @var Logger
     */
    public $logger;

    /**
     * @Inject
     * @var ReduxConfigurator
     */
    public $redux;

    /**
     * @Inject
     * @var Engine
     */
    public $template;

    /**
     * @Inject
     * @var Tracker
     */
    public $snowTracker;

    /**
     * @Inject
     * @var MultiLang
     */
    public $multiLang;

    /**
     * @Inject
     * @var Router
     */
    public $router;

    /**
     * @Inject
     * @var Assets
     */
    public $assets;

    /**
     * @Inject
     * @var HooksLoader
     */
    public $hooksLoader;

    /**
     * @Inject
     * @var I18n
     */
    protected $i18n;

    /**
     * @Inject
     * @var Rights
     */
    public $userRights;

    /**
     * @var Tables
     */
    public $tables;

    /**
     * @var Widgets
     */
    public $widgets;

    /**
     * @var SearchFormModule
     */
    public $searchForms;

    /**
     * @Inject
     * @var Notices
     */
    public $notices;

    /**
     * @var HelpModule
     */
    protected $helpModule;

    /**
     * @var Links
     */
    public $links;

    /**
     * @var MoneyScriptModule
     */
    public $moneyScript;
    /**
     * @Inject
     * @var Request
     */
    public $request;

    public function init()
    {
        try {
            $this->checkDependencies();
            $this->i18n->register();
            $this->translator->locale = LanguageHelper::tableLocale();
            $this->hooksLoader
                ->registerHooksInstance($this->assets->loader)
                ->registerHooksInstance(new AdminHooks())
                ->registerHooksInstance(new PublicHooks());

            $this->createModules();
            $this->registerShortcodes();
        } catch (TravelpayoutsException $e) {
            if (!(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) && !wp_doing_ajax()) {
                (new ExceptionRenderer($e))->render();
            } else {
                throw $e;
            }
        }

    }

    protected function checkDependencies()
    {
        if ($this->i18n === null) {
            Deactivator::deactivate();
            throw new TravelpayoutsException(
                implode("\n", [
                    __('Please make sure or contact your hosting support php.ini setting must be set this way'),
                    '<pre>opcache.save_comments=1;</pre>',
                    __('<a href="https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.save-comments" target="_blank">Read more</a>'),
                ])
            );

        }
    }

    protected function createModules()
    {
        $this->tables = new Tables();
        $this->widgets = new Widgets();
        $this->searchForms = new SearchFormModule();
        $this->helpModule = new HelpModule();
        $this->links = new Links();
        $this->moneyScript = new MoneyScriptModule();
    }

    /**
     * @inheritDoc
     */
    protected function aliasList()
    {
        $uploadDir = wp_upload_dir();

        return [
            '@root' => TRAVELPAYOUTS_PLUGIN_PATH,
            '@data' => TRAVELPAYOUTS_PLUGIN_PATH . '/data',
            '@src' => TRAVELPAYOUTS_PLUGIN_PATH . '/src',
            '@config' => TRAVELPAYOUTS_PLUGIN_PATH . '/src/config',
            '@includes' => TRAVELPAYOUTS_PLUGIN_PATH . '/src/includes',
            '@admin' => TRAVELPAYOUTS_PLUGIN_PATH . '/src/admin',
            '@assets' => TRAVELPAYOUTS_PLUGIN_PATH . '/assets',
            '@images' => TRAVELPAYOUTS_PLUGIN_PATH . '/images',
            '@webhome' => get_home_url(),
            '@webroot' => plugin_dir_url(TRAVELPAYOUTS_PLUGIN_PATH . '/src'),
            '@webadmin' => '@webroot/src/admin',
            '@uploads' => !$uploadDir['error']
                ? $uploadDir['basedir'] . DIRECTORY_SEPARATOR . TRAVELPAYOUTS_PLUGIN_NAME
                : null,
            '@webuploads' => !$uploadDir['error']
                ?
                $uploadDir['baseurl'] . DIRECTORY_SEPARATOR . TRAVELPAYOUTS_PLUGIN_NAME
                : null,
            '@runtime' => '@uploads',
            '@webImages' => '@webroot/images',
        ];
    }

    /**
     * @return ModuleRedux[] | Module[]
     */
    public function getReduxModules()
    {
        return [
            $this->tables,
            $this->widgets,
            $this->searchForms,
            $this->moneyScript,
            $this->account,
            $this->settings,
            $this->helpModule,
            $this->links,
        ];
    }

    protected function registerShortcodes()
    {
        foreach ($this->getReduxModules() as $module) {
            if (method_exists($module, 'registerShortcodes')) {
                $module->registerShortcodes();
            }
        }
    }

    /**
     * Translates the given message.
     * @param string $id The message id (may also be an object that can be cast to string)
     * @param array $parameters An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     * @return string The translated string
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public static function t($id, array $parameters = [], $domain = null, $locale = null)
    {
        return self::getInstance()->translator->trans($id, $parameters, $domain, $locale);
    }

}
