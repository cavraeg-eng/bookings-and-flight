<?php

namespace Travelpayouts\components\tables\enrichment;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\settings\SettingsForm;
use Travelpayouts\traits\SingletonTrait;

class UrlHelper extends BaseInjectedObject
{
    use SingletonTrait;

    public const TUTU_CUSTOM_URL_HOST = 'www.tutu.ru/poezda/rasp_d.php';
    public const TUTU_URL_HOST = 'c45.travelpayouts.com/click';
    public const TUTU_PROMO_ID = 4483;
    protected const MEDIA_URL = 'tp.media/r';
    public const FLIGHT_P = 4462;
    public const HOTELS_P = 4463;
    public const LINKS_P = 4114;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    protected $useRedirect = false;

    public function init()
    {
        if (StringHelper::toBoolean($this->globalSettings->redirect)) {
            $this->useRedirect = true;
        }
    }

    public static function get_marker($marker, $subid, $type)
    {
        $markerParams = [
            $marker,
            !empty($subid) ? "{$subid}_{$type}" : $type,
            '$69',
        ];
        return htmlspecialchars(implode('.', $markerParams));
    }

    public static function buildUrl($rawHost, $params)
    {
        $host = self::addScheme($rawHost);

        return $host . '?' . http_build_query(array_filter($params));
    }

    public static function buildMediaUrl($params)
    {
        return self::buildUrl(self::MEDIA_URL, $params);
    }

    public static function addScheme($url, $scheme = 'https://')
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            return $scheme . $url;
        } else {
            return $url;
        }
    }

    /**
     * Формируем ссылку исходя из пользовательских настроек редиректа
     * @param string $url
     * @return string
     */
    public function getUrl(string $url): string
    {
        if ($this->useRedirect) {
            $queryString = [
                $this->getRedirectParamName() => urlencode($url),
            ];
            return get_site_url(get_current_blog_id(), '?' . http_build_query($queryString));
        }
        return $url;
    }

    protected function getRedirectParamName(): string
    {
        return TRAVELPAYOUTS_TEXT_DOMAIN . '_redirect';
    }

    /**
     * Redirect domain/travelpayouts_redirect?https://google.com
     */
    public function externalRedirectAction(){
        if($this->useRedirect){
            $redirectUrl = \Travelpayouts::getInstance()->request->getQueryParam($this->getRedirectParamName());
            if($redirectUrl){
                wp_safe_redirect(urldecode($redirectUrl), 301);
                exit;
            }
        }
    }
}
