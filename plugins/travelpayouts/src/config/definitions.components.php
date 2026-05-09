<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */
use Travelpayouts\Vendor\League\Plates\Engine;
use Rollbar\Rollbar;
use Rollbar\RollbarLogger;
use Travelpayouts\admin\AdminHooks;
use Travelpayouts\admin\components\AirtableDistribution;
use Travelpayouts\components\Assets;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\base\cache\CacheFromSettings;
use Travelpayouts\components\Logger;
use Travelpayouts\components\multilingual\MultiLang;
use Travelpayouts\components\Rights;
use Travelpayouts\components\snowplow\Tracker;
use Travelpayouts\components\Translator;
use Travelpayouts\frontend\PublicHooks;
use Travelpayouts\includes\HooksLoader;
use Travelpayouts\includes\I18n;
use Travelpayouts\includes\PublicRouter;
use Travelpayouts\includes\ReduxConfigurator;
use Travelpayouts\includes\Router;
use function Travelpayouts\Vendor\DI\get;
use function Travelpayouts\Vendor\DI\autowire;

return [
    Cache::class => autowire(CacheFromSettings::getCacheClass()),
    PublicHooks::class => autowire(),
    AdminHooks::class => autowire(),
    MultiLang::class => autowire(),
    Assets::class => autowire(Assets::class)->constructor([
        'path' => Travelpayouts::getAlias('@root/assets/assets.json'),
        'prefix' => TRAVELPAYOUTS_PLUGIN_NAME . '-assets',
        'chunkNameVariableName' => 'travelpayouts_loadable_chunks',
    ]),
    Translator::class => autowire()->constructor([
        'defaultLocale' => 'en',
        'translationsFolder' => '@data/translations',
    ]),
    Router::class => autowire()->constructor([
        'actionParam' => 'wp_ajax_travelpayouts_routes',
    ]),
    PublicRouter::class => autowire()->constructor([
        'actionParam' => 'wp_ajax_nopriv_travelpayouts_routes',
    ]),
    /** @see Engine::addFolder() */
    Engine::class => autowire()->method('addFolder', 'admin', Travelpayouts::getAlias('@src/admin/templates')),
    ReduxConfigurator::class => autowire()->constructor(TRAVELPAYOUTS_REDUX_OPTION, get('redux.config')),
    Logger::class => autowire()->constructor(),
    Tracker::class => autowire()->constructor([
        'url' => TRAVELPAYOUTS_DEBUG
            ? 'beta.avsplow.com'
            : 'avsplow.com',
        'protocol' => 'https',
        'post_type' => 'POST',
        'buffer_size' => 1,
        'debug' => TRAVELPAYOUTS_DEBUG,
        'namespace' => null,
        'app_id' => 'tp_wp_plugin',
    ])->method('setContext', get('snowplow.context')),
    HooksLoader::class => autowire(),
    I18n::class => autowire()->constructor([
        'domain' => TRAVELPAYOUTS_TEXT_DOMAIN,
        'localePath' => Travelpayouts::getAlias('@root/languages'),
    ]),
    Rights::class => autowire(),
    AirtableDistribution::class => autowire()->constructor([
        'baseId' => 'appdZVIFV8Fj9j9OU',
        'tableId' => 'tblve8a7LnLegx49h',
        'token' => defined('TRAVELPAYOUTS_AIRTABLE_TOKEN') ? TRAVELPAYOUTS_AIRTABLE_TOKEN : '',
    ]),
];
