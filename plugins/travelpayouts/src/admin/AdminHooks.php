<?php

/**
 * The admin-specific functionality of the plugin.
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/admin
 */

namespace Travelpayouts\admin;

use Exception;
use Travelpayouts;
use Travelpayouts\admin\components\DeactivationFeedback;
use Travelpayouts\admin\components\EditorHooks;
use Travelpayouts\admin\components\landingPage\LandingModel;
use Travelpayouts\admin\controllers\NotificationController;
use Travelpayouts\admin\partials\LandingPage;
use Travelpayouts\admin\redux\extensions\SettingsImportField;
use Travelpayouts\admin\redux\ReduxHooks;
use Travelpayouts\components\Assets;
use Travelpayouts\components\brands\PlatformsEndpoint;
use Travelpayouts\components\Logger;
use Travelpayouts\components\Menu;
use Travelpayouts\components\notices\Notice;
use Travelpayouts\components\notices\NoticeButton;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\components\Rights;
use Travelpayouts\components\snowplow\Tracker;
use Travelpayouts\includes\HooksLoader;
use Travelpayouts\includes\migrations\Migration;
use Travelpayouts\includes\ReduxConfigurator;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\settings\Settings;

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @package    Travelpayouts
 * @subpackage Travelpayouts/admin
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 */
class AdminHooks extends Travelpayouts\components\HookableObject
{
    /**
     * The ID of this plugin.
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     * @Inject("name")
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     * @Inject("version")
     */
    protected $version;

    /**
     * @Inject
     * @var Logger
     */
    public $logger;

    /**
     * @Inject
     * @var Tracker
     */
    public $snowTracker;

    /**
     * @Inject
     * @var Assets
     */
    public $assets;

    /**
     * @Inject
     * @var ReduxConfigurator
     */
    protected $redux;

    /**
     * @Inject
     * @var Account
     */
    protected $account;

    /**
     * @Inject
     * @var Notices
     */
    public $notices;

    /**
     * @Inject
     * @var Rights
     */
    public $userRights;

    /**
     * @var Settings
     * @Inject
     */
    protected $settings;

    public function init()
    {
        // Инициализируем контроллер уведомлений
        new NotificationController();
    }

    /**
     * @inheritDoc
     */
    protected function hookList(HooksLoader $hooksLoader)
    {
        $hooksLoader
            ->registerHooksInstance(new EditorHooks())
            ->addAdminAjaxEndpoint('travelpayouts_import_file', [$this, 'importFile'])
            ->addAdminAjaxEndpoint('travelpayouts_clear_tables_cache', [$this, 'clearTablesCache'])
            ->addAdminAjaxEndpoint('travelpayouts_migrate', [$this, 'migrate'])
            ->addAdminAjaxEndpoint('travelpayouts_migrate_cancel', [$this, 'migrateCancel'])
            ->addAdminAjaxEndpoint('travelpayouts_clear_platforms_select_cache', [
                $this,
                'clearPlatformsSelectCache',
            ])
            ->addAdminAjaxEndpoint('travelpayouts_migrate_search_forms', [$this, 'migrateSearchForms'])
            ->addAction('plugins_loaded', [$this, 'loadReduxOptions'])
            ->addAction('admin_menu', [$this->get_landing_page(), 'add_page'])
            ->addAction('admin_notices', [$this, 'renderNotices'])
            ->addAction('redux_travelpayouts/page/' . TRAVELPAYOUTS_REDUX_OPTION . '/enqueue', [
                $this,
                'overrideReduxCss',
            ])
            ->addAction(
                'admin_action_' . LandingPage::ACTION, [
                $this,
                'landing_page_action',
            ])
            ->addAction(
                'update_option_' . TRAVELPAYOUTS_REDUX_OPTION,
                [
                    $this,
                    'trackMarkerChanged',
                ],
                10,
                2
            )
            ->addAction('init', [$this, 'pluginVersionAction'])
            ->addAction(
                'update_option_' . TRAVELPAYOUTS_VERSION_KEY,
                [
                    $this,
                    'trackVersionChanged',
                ],
                10,
                2
            )->addAction('current_screen', [new DeactivationFeedback(), 'run']);

    }

    public function loadReduxOptions()
    {
        if ($this->userRights->manage_options) {
            $opt = new ReduxHooks();
            $opt->setUpHooks();
        }
    }

    /**
     * @param $old
     * @param $new
     */
    public function trackMarkerChanged($old, $new)
    {
        if (
            isset($old['account_api_marker'], $new['account_api_marker']) && $old['account_api_marker'] !== $new['account_api_marker']
        ) {
            $this->snowTracker->trackStructEvent(
                Tracker::CATEGORY_INSTALL,
                Tracker::ACTION_ACTIVATED,
                null,
                null,
                null,
                [
                    'marker_old' => $old['account_api_marker'],
                    'marker' => $new['account_api_marker'],
                ]
            );
        }
    }

    /**
     * @param $old
     * @param $new
     */
    public function trackVersionChanged($old, $new)
    {
        if (version_compare($new, $old, '>')) {
            $this->snowTracker->trackStructEvent(
                Tracker::CATEGORY_INSTALL,
                Tracker::ACTION_UPDATED,
                null,
                null,
                null,
                [
                    'plugin_version_previous' => $old,
                    'marker' => $this->account->getMarker(),
                ]
            );

            $this->importOnUpdate($old, $new);
        }
    }

    public function pluginVersionAction()
    {
        // Если есть версия прошлого плагина будет виден апдейт
        // TRAVELPAYOUTS_VERSION_KEY то же значение что и в прошлой версии
        $pluginOptionVersion = get_option(TRAVELPAYOUTS_VERSION_KEY);

        if (empty($pluginOptionVersion) || $pluginOptionVersion != TRAVELPAYOUTS_VERSION) {
            update_option(TRAVELPAYOUTS_VERSION_KEY, TRAVELPAYOUTS_VERSION);
        }
    }

    public function overrideReduxCss()
    {
        wp_dequeue_style('redux-admin-css');
    }

    public function migrate()
    {
        /**
         * import on plugin update
         * add_action( 'upgrader_process_complete', function( $upgrader_object, $options ) {
         *      import here
         * }, 10, 2 );
         */

        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        $options = get_option(Migration::SOURCE_OPTION_NAME);
        $importDone = get_option(Migration::IMPORT_DONE_OPTION_NAME);

        if ($options && $importDone != Migration::IMPORT_DONE_TRUE) {
            $this->importModel()->import();
        }

        echo json_encode([
            'status' => 'success',
            'action' => 'reload',
        ]);
        die ();
    }

    public function migrateCancel()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        update_option(Migration::IMPORT_DONE_OPTION_NAME, Migration::IMPORT_DONE_TRUE);

        echo json_encode([
            'status' => 'success',
            'action' => 'reload',
        ]);
        die ();
    }

    public function clearPlatformsSelectCache()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        if ($response = PlatformsEndpoint::getInstance()->getResponse()) {
            $response->deleteCache();
        }

        echo json_encode([
            'status' => 'success',
            'action' => 'reload',
        ]);
        die ();
    }

    public function migrateSearchForms()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }
        $this->importModel()->importSearchForms();

        echo json_encode([
            'status' => 'success',
            'action' => 'reload',
        ]);
        die ();
    }

    /**
     * @throws Exception
     */
    public function clearTablesCache()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        global $wpdb;

        echo json_encode([
            'action' => 'reload',
        ]);

        try {
            $table = $wpdb->get_blog_prefix(get_current_blog_id()) . 'options';
            $sql = "DELETE FROM $table WHERE option_name LIKE \"%_transient_travelpayouts%\" OR option_name LIKE \"%_transient_timeout_travelpayouts%\";";

            $result = $wpdb->query($sql);

            if ($result === false) {
                throw new Exception('Clear cache error');
            }
        } catch (Exception $exception) {
            $this->notices->add(
                Notice::create('redux-clear-cache-notice')
                    ->setType(Notice::NOTICE_TYPE_ERROR)
                    ->setTitle(Travelpayouts::__('Clear cache failed'))
                    ->setDescription($exception->getMessage())
            );
            die();
        }

        $this->notices->add(
            Notice::create('redux-clear-cache-notice')
                ->setType(Notice::NOTICE_TYPE_SUCCESS)
                ->setTitle(Travelpayouts::__('Cache has been cleared'))
                ->setDescription(Travelpayouts::__('Tables cache has been cleared successfully'))
        );
        die();
    }

    public function importFile()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[SettingsImportField::NONCE_NAME])), SettingsImportField::TYPE)) {
            wp_die('Nonce verification failed.');
        }

        if (isset($_POST['settings']) && !empty($_POST['settings'])) {
            try {
                $this->importModel($_POST['settings'])->import();
            } catch (Exception $exception) {
                $this->notices->add(
                    Notice::create('redux-import-notice')
                        ->setType(Notice::NOTICE_TYPE_ERROR)
                        ->setTitle(Travelpayouts::__('Import failed'))
                );
                die ();
            }
        }

        $this->notices->add(
            Notice::create('redux-import-notice')
                ->setType(Notice::NOTICE_TYPE_SUCCESS)
                ->setTitle(Travelpayouts::__('Import completed'))
        );

        echo json_encode([
            'status' => 'success',
            'action' => 'reload',
        ]);
        die ();
    }

    /**
     * @param $old
     * @param $new
     */
    public function importOnUpdate($old, $new)
    {
        if (version_compare($old, '0.7.13', '<=')) {
            $importModel = $this->importModel();
            // Импортирует все настройки после перехода с 0.7.13
            $importModel->importSettings();
            // импортируем поисковые формы, если поисковая форма есть, пропускаем
            $importModel->importSearchForms();
        }
    }

    public function get_landing_page()
    {
        return new Menu(
            new LandingPage($this),
            LandingModel::LANDING_SLUG,
            Travelpayouts::__('Travelpayouts WordPress plugin'),
            Travelpayouts::_x('Travelpayouts', 'Travelpayouts')
        );
    }

    public function renderNotices()
    {

        $screen = get_current_screen();
        // Не показывать уведомления плагина в редакторе
        if ( ! $screen || 'post' === $screen->base ) {
            return;
        }

        if ($this->userRights->manage_options) {
            // Добавляет welcome (плашку) уведомление
            $this->welcomeNotice();
            $this->importNotice();
            $this->platformNotice();
            $this->transientNotice();
            // Получает и отображает уведомления
            $this->notices->render();
        }
    }

    public function landing_page_action()
    {
        if (!$this->userRights->manage_options) {
            die(Travelpayouts::__('Insufficient access rights!'));
        }

        $postData = wp_unslash($_POST);
        $nonce = isset($postData['_wpnonce']) ? sanitize_text_field($postData['_wpnonce']) : '';

        if (wp_verify_nonce($nonce, LandingPage::ACTION)) {
            $model = new LandingModel();
            $model->setSanitizedAttributes($postData);
            $model->save();

            $redirect = isset($postData['_wp_http_referer']) ? esc_url_raw($postData['_wp_http_referer']) : admin_url();
            wp_safe_redirect($redirect);
            exit;
        }

        die(Travelpayouts::__('WP nonce verification failed!'));
    }

    /**
     * Уведомление is-active-redux-notice
     * Добавляет уведомление если redux plugin не активирован
     * пустое значения API token или affiliate marker в настройках аккаунта
     * @return bool
     */
    private function welcomeNotice()
    {
        if (function_exists('get_current_screen')) {
            $page = get_current_screen();

            // не отображать на страницах landing, активация плагина
            $pages = [
                'plugins_page_install-required-plugins',
                'settings_page_' . LandingModel::LANDING_SLUG,
            ];
            if (in_array($page->base, $pages)) {
                return true;
            }
        }

        if (empty($this->account->getToken()) || empty($this->account->getMarker())) {
            $this->notices->add(
                Notice::create('is-active-redux-notice')
                    ->setTitle(Travelpayouts::__('Activate the Travelpayouts plugin'))
                    ->setDescription(Travelpayouts::__('Enter your Travelpayouts authorization details and start earning now'))
                    ->addButton(
                        NoticeButton::create(Travelpayouts::__('Activate the plugin'))
                            ->setType(NoticeButton::BUTTON_TYPE_PRIMARY)
                            ->setUrl(wp_nonce_url(add_query_arg(['page' => LandingModel::LANDING_SLUG], admin_url('options-general.php'))))
                    )
                    ->setCloseable()
            );
        }
        return true;
    }

    /**
     * Уведомление redux-import-action-notice предлагает импортировать настройки из старой версии
     * @return void
     */
    private function importNotice()
    {
        $this->assets->loader->registerAsset('admin-notice');
        $sourceOption = get_option(Migration::SOURCE_OPTION_NAME);
        $canImport = get_option(Migration::IMPORT_DONE_OPTION_NAME) != Migration::IMPORT_DONE_TRUE;

        if ($sourceOption && $canImport) {
            $this->assets->loader->registerAsset('admin-migrate');
            $this->notices->add(
                Notice::create('redux-import-action-notice')
                    ->setTitle(Travelpayouts::__('Import settings from the previous version'))
                    ->setDescription(Travelpayouts::__('You updated the Travelpayotus WordPress plugin. This is a new version and has differences with your previous one. To be sure that your existing settings were imported correctly, you have to confirm and click the button on the right. Please double-check after importing.'))
                    ->addButton(
                        NoticeButton::create(Travelpayouts::__(Travelpayouts::__('Skip import')))
                            ->setType(NoticeButton::BUTTON_TYPE_SECONDARY)
                            ->setClassName('travelpayouts-migrate-cancel')
                    )
                    ->addButton(
                        NoticeButton::create(Travelpayouts::__('Import settings'))
                            ->setType(NoticeButton::BUTTON_TYPE_PRIMARY)
                            ->setClassName('travelpayouts-migrate')
                    )
                    ->setCloseable()
            );
        }
    }

    /**
     * Уведомление transient-cache-notice предлагает изменить настройки кэширования
     * @return void
     */
    private function transientNotice()
    {
        $useFileCache = $this->settings->getUseFileCache();

        if (!$useFileCache) {
            global $wpdb;

            $prefix = $wpdb->esc_like('_transient_travelpayouts');
            $sql = "SELECT COUNT(*) FROM $wpdb->options WHERE `option_name` LIKE '%s'";
            $count = $wpdb->get_var($wpdb->prepare($sql, $prefix . '%'));

            // TODO change count and notice text
            if (!empty($count) && $count > 1) {
                $this->notices->add(
                    Notice::create('transient-cache-notice')
                        ->setTitle(Travelpayouts::__('Transient cache could slow down your resource!'))
                        ->setDescription(Travelpayouts::__('We highly recommend you to use our file cache feature to decrease transient cache records in your options table.'))
                        ->addButton(
                            NoticeButton::create(Travelpayouts::__('Plugin settings'))
                                ->setType(NoticeButton::BUTTON_TYPE_PRIMARY)
                                ->setUrl(add_query_arg([
                                    'page' => 'travelpayouts_options',
                                    'section' => 'settings',
                                    'field'=> 'settings_use_fileCache'
                                ], admin_url('admin.php')))
                        )
                        ->setCloseable()
                );
            }
        }
    }

    /**
     * Уведомление account-platform-selected-notice требует выбрать площадку в настройках
     * @return void
     */
    private function platformNotice()
    {
        $platforms = PlatformsEndpoint::getInstance();
        if ($platforms->showSelectPlatformNotice()) {
            $this->notices->add(
                Notice::create('account-platform-selected-notice')
                    ->setTitle(Travelpayouts::__('Action is required!'))
                    ->setDescription(Travelpayouts::__('Choose a traffic source<br>To correctly track your stats for your website, please select a traffic source it belongs to in the settings'))
                    ->addButton(
                        NoticeButton::create(Travelpayouts::__('Account settings'))
                            ->setType(NoticeButton::BUTTON_TYPE_PRIMARY)
                            ->setUrl(add_query_arg([
                                'page' => 'travelpayouts_options',
                                'section' => 'account',
                                'field'=> 'account_platform'
                            ], admin_url('admin.php')))
                    )
                    ->setCloseable()
            );
        }

        if (!$platforms->isActiveRequiredPrograms()) {
            $this->notices->add(
                Notice::create('account-program-required-notice')
                    ->setTitle(Travelpayouts::__('Action is required!'))
                    ->setDescription(Travelpayouts::__('Please join Aviasales program with selected project in the partners dashboard to add this tool.'))
                    ->addButton(
                        NoticeButton::create(Travelpayouts::__('Activate programs'))
                            ->setType(NoticeButton::BUTTON_TYPE_PRIMARY)
                            ->setUrl('https://www.travelpayouts.com/programs')
                            ->openInNewWindow()
                    )
            );
        }
    }

    /**
     * @param null $source
     * @return Migration
     */
    protected function importModel($source = null)
    {
        if ($source === null) {
            $source = get_option(Migration::SOURCE_OPTION_NAME);
        }

        return new Migration([
            'redux' => $this->redux,
            'source' => $source,
        ]);
    }
}
