<?php

namespace Travelpayouts\modules\links\components;

use DateTime;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts;
use Travelpayouts\components\ErrorHelper;
use Travelpayouts\components\HtmlHelper as Html;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\modules\account\Account;
use Travelpayouts\modules\settings\Settings;

/**
 * Class LinkModel
 */
abstract class BaseLinkShortcode extends ShortcodeModel
{
    const LINK_MARKER = 'wpplugin_link';

    /**
     * @var string
     */
    public $new_tab = null;
    /**
     * @var string
     */
    public $text_link;
    /**
     * @var string
     */
    protected $subid;

    /**
     * @Inject
     * @var Settings
     */
    protected $settingsModule;

    /**
     * @Inject
     * @var Account
     */
    protected $accountModule;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text_link'], 'required'],
            [['shortcode_name', 'new_tab'], 'safe'],
            [['subid'], 'string'],
        ]);
    }

    /**
     * Добавляет количесто дней из параметра $days к текущей дате
     * @param int $days
     * @param string $format
     * @return string
     */
    protected function date_time_add_days($days = 1, $format = 'Y-m-d')
    {
        try {
            $date_time = new DateTime();
            $date_time->modify('+' . $days . ' Day');

            return $date_time->format($format);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Формирование ссылки из параметра url и настроек
     * @param $href
     * @return string
     */
    protected function createAnchorTag($href): string
    {
        $buttonAttributes = [
            'href' => $href,
        ];
        $settingsModuleData = $this->settingsModule->data;
        $settingsNewTab = true === filter_var(
                $settingsModuleData->get('target_url'),
                FILTER_VALIDATE_BOOLEAN
            );
        if ($settingsModuleData->get('nofollow')) {
            $buttonAttributes['rel'] = 'nofollow';
        }

        if ($settingsNewTab) {
            $buttonAttributes['target'] = '_blank';
        }

        if ($this->new_tab != null) {
            $newTab = true === filter_var(
                $this->new_tab,
                FILTER_VALIDATE_BOOLEAN
            );
            if ($newTab) {
                $buttonAttributes['target'] = '_blank';
            } else {
                unset($buttonAttributes['target']);
            }
        }

        $buttonAttributes['class'] = TRAVELPAYOUTS_TEXT_DOMAIN . '-link';

        return Html::tag(
            'a',
            $buttonAttributes,
            $this->text_link
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $url = UrlHelper::getInstance()->getUrl($this->getUrl());
        return $this->createAnchorTag($url);
    }

    abstract protected function getUrl(): ?string;

    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $model = new static();
        $model->attributes = $attributes;
        if (!$model->validate()) {
            return ErrorHelper::render_errors($tag, $model->getErrors());
        }
        return $model->render();
    }

    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'new_tab' => Travelpayouts::__('Open link in a new tab'),
            'subid' => Travelpayouts::__('Sub ID'),
        ]);
    }

    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(), [
            'text_link' => $this->fieldInput(),
            'new_tab' => $this->fieldCheckbox()->setDefault(true),
            'subid' => $this->fieldInput(),
        ]);
    }

}
