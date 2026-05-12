<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux;
use Travelpayouts\Vendor\League\Plates\Engine;
use Travelpayouts;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\includes\HooksLoader;

class ReduxHooks extends Travelpayouts\components\HookableObject
{
    /**
     * @Inject
     * @var Engine
     */
    public $template;

    public $optName = TRAVELPAYOUTS_REDUX_OPTION;

    /**
     * @inheritDoc
     */
    protected function hookList(HooksLoader $hooksLoader)
    {
        if (class_exists('Redux_Travelpayouts')) {
            $optName = $this->optName;
            $hooksLoader->addAction("redux_travelpayouts/page/$optName/form/before", function () {
                echo $this->renderFeedbackForm();
            });

            $this->registerModuleSections();
        }
    }

    /**
     * Инициализируем формы из модулей
     */
    protected function registerModuleSections()
    {
        foreach (Travelpayouts::getInstance()->getReduxModules() as $module) {
            if ($module instanceof ModuleRedux && $module->isActive()) {
                $module->registerSection();
            }
        }
    }

    protected function renderFeedbackForm()
    {
        $locale = LanguageHelper::userLocale(false);

        $isRuLocale = $locale === 'ru_RU';
        $currentUser = wp_get_current_user();

        return $this->template->render('admin::feedbackButton', [
            'buttonTitle' => $isRuLocale
                ? 'Сообщить о баге / оставить отзыв'
                : 'Report a bug / leave your feedback',
            'formId' => $isRuLocale
                ? 'HiJ9Gz8U'
                : 'pKhiBqhm',
            'buttonParams' => [
                'wp_version' => get_bloginfo('version'),
                'php_version' => PHP_VERSION,
                'plugin_version' => defined('TRAVELPAYOUTS_VERSION')
                    ? TRAVELPAYOUTS_VERSION
                    : '-',
                'domain' => home_url(),
                'plugin_locale' => $locale,
                'marker' => Travelpayouts::getInstance()->account->marker,
                'email' => $currentUser->user_email,
            ],
        ]);
    }
}
