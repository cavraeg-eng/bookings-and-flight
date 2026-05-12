<?php

namespace Travelpayouts\modules\widgets\components;

use Travelpayouts\components\brands\PlatformsEndpoint;
use Travelpayouts\components\shortcodes\ShortcodeModel;

/**
 * Class WidgetShortcode
 * @package Travelpayouts\modules\widgets\components
 */
class WidgetShortcode extends ShortcodeModel
{
    public const TRAVELPAYOUTS_SHORTCODE_REGEX = '/^<script[^>]* src="(?<url>[^"]+\/content\?([^"]+)).*?<\/script>$/';

    /**
     * @var string|null
     */
    public $content;

    /**
     * @var null|string
     */
    protected $_scriptUrl = null;

    public static function shortcodeTags()
    {
        return ['tp_widget'];
    }

    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new self();
        if (is_string($content)) {
            $model->content = $content;
        }
        return $model->render();
    }

    public function render()
    {
        if ($this->getScriptUrl() && is_string($this->content)) {
            return $this->getShortcodeContentWithReplacedScriptDomain($this->content);
        }
        return '';
    }

    protected function getShortcodeContentWithReplacedScriptDomain(string $content): string
    {
        $scriptHost = $this->getScriptHost();
        if ($scriptHost) {
            $platformResponse = PlatformsEndpoint::getInstance()->getData();
            $widgetDomain = $platformResponse ? $platformResponse->widget_domain : null;
            if ($widgetDomain) {
                $content = str_replace($scriptHost, $widgetDomain, $content);
            }
        }
        return $content;
    }

    /**
     * Проверяем, является ли переданный контент виджетом Travelpayouts
     * @param $data
     * @return bool
     */
    public static function isTravelpayoutsWidget($data): bool
    {
        return is_string($data) && preg_match(self::TRAVELPAYOUTS_SHORTCODE_REGEX, trim($data));
    }

    protected function getScriptUrl(): ?string
    {
        if (!$this->_scriptUrl && $this->content) {
            $content = html_entity_decode(trim($this->content));
            if (preg_match(self::TRAVELPAYOUTS_SHORTCODE_REGEX, $content, $matches)) {
                $this->_scriptUrl = $matches['url'];
            }
        }
        return $this->_scriptUrl;
    }

    /**
     * Получаем параметры виджета из URL скрипта
     * @return array
     */
    public function getWidgetParameters(): array
    {
        if ($scriptUrl = $this->getScriptUrl()) {
            $parsedUrl = parse_url($scriptUrl);
            if (isset($parsedUrl['query'])) {
                $queryParams = [];
                parse_str($parsedUrl['query'], $queryParams);
                return $queryParams;
            }
        }
        return [];
    }

    /**
     * Получаем домен скрипта виджета
     * @return string
     */
    public function getScriptHost(): ?string
    {
        if ($scriptUrl = $this->getScriptUrl()) {
            $host = parse_url($scriptUrl, PHP_URL_HOST);
            return $host ?: null;
        }
        return null;
    }
}