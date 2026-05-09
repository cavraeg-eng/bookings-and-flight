<?php

namespace Travelpayouts\admin\partials;

use Travelpayouts;
use Travelpayouts\admin\AdminHooks;
use Travelpayouts\admin\components\landingPage\LandingModel;
use Travelpayouts\components\LanguageHelper;

/**
 * Class LandingPage
 * @package Travelpayouts\admin\partials
 */
class LandingPage
{
    const ACTION = 'travelpayouts_landing_settings';

    protected $_admin;
    protected $_model;

    public function __construct(AdminHooks $pluginAdmin)
    {
        $this->_admin = $pluginAdmin;
        $this->_model = new LandingModel();
    }

    /**
     * @param $image
     * @return bool|string
     * @throws \Exception
     */
    public static function getLandingImage($image)
    {
        return Travelpayouts::getAlias('@webImages/admin/landing/' . $image);
    }

    /**
     * @param $url
     * @param $args
     * @return string
     */
    private function generatePluginActionUrl($url, $args)
    {
        return wp_nonce_url(
            add_query_arg(
                $args,
                admin_url($url)
            )
        );
    }

    /**
     * @return string
     */
    private function installPluginUrl()
    {
        return $this->generatePluginActionUrl('plugins.php', ['page' => 'install-required-plugins']);
    }

    /**
     * @param $base
     * @param $params
     * @return string
     */
    private function getUtmLink($base, $params)
    {
        return $base . '?' . http_build_query($params);
    }

    /**
     * @return string
     */
    private function apiTokenUrl()
    {
        return $this->getUtmLink(
            'https://app.travelpayouts.com/profile/api-token',
            [
                'utm_source' => 'wpplugin',
                'utm_medium' => 'settings',
                'utm_campaign' => LanguageHelper::dashboardLocale(),
                'utm_content' => 'link_api'
            ]
        );
    }

    /**
     * @return string
     */
    private function createAccountUrl()
    {
        return $this->getUtmLink(
            'https://www.travelpayouts.com/auth/registration',
            [
                'utm_source' => 'wpplugin',
                'utm_medium' => 'settings',
                'utm_campaign' => LanguageHelper::dashboardLocale(),
                'utm_content' => 'link'
            ]
        );
    }

    public function render()
    {
        echo Travelpayouts::getInstance()->template->render('admin::landingPage/landing', [
            'isRU' => LanguageHelper::isRuDashboard(),
            'createAccountUrl' => $this->createAccountUrl(),
            'apiTokenUrl' => $this->apiTokenUrl(),
            'action' => self::ACTION,
            'landingModel' => $this->_model,
            'installPluginUrl' => $this->installPluginUrl()
        ]);
    }
}
