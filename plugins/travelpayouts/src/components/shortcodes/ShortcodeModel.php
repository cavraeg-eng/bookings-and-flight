<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\shortcodes;

use Travelpayouts;
use Travelpayouts\components\InjectedModel;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\rest\traits\GutenbergFieldTrait;
use Travelpayouts\components\rest\traits\ShortcodeGutenbergTrait;
use Travelpayouts\helpers\ArrayHelper;

abstract class ShortcodeModel extends InjectedModel implements IShortcodeModel
{
    use ShortcodeGutenbergTrait;
    use GutenbergFieldTrait;

    public const SCENARIO_GENERATE_SHORTCODE = 'restShortcodeGenerator';

    /**
     * @var string
     */
    public $tag;

    public function rules()
    {
        return [
            [['_'], 'in', 'range' => [], 'except' => [self::SCENARIO_GENERATE_SHORTCODE],],
        ];
    }

    /**
     * @see render_shortcode_static()
     */
    public static function register(): void
    {
        if (is_array(static::shortcodeTags())) {
            foreach (static::shortcodeTags() as $shortcodeTag) {
                $isActive = static::isActive();
                $hooksLoader = Travelpayouts::getInstance()->hooksLoader;
                if ($isActive) {
                    $hooksLoader->addShortcode(
                        $shortcodeTag,
                        [
                            static::class,
                            'render_shortcode_static',
                        ]
                    );
                } else {
                    // Отдаем пустой шорткод, если не активен
                    $hooksLoader->addShortcode(
                        $shortcodeTag,
                        function () {
                            return '';
                        }
                    );
                }

            }
        }
    }

    /**
     * @return string|null
     */
    abstract public function render();

    /**
     * @return bool
     */
    public static function isActive(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function gutenbergFields(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function shortcodeName(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function extraFields(): array
    {
        return [
            'id' => function () {
                return ArrayHelper::getFirst(static::shortcodeTags());
            },
            'label' => function () {
                return $this->shortcodeName();
            },
            'fields' => function () {
                return $this->getGutenbergFields();
            },
            'extraData' => function () {
                return $this->gutenbergExtraData();
            },
        ];
    }

    /**
     * @return array
     */
    public function gutenbergExtraData(): array
    {
        return [];
    }

    /**
     * @param $errorsList
     * @return string
     */
    protected function renderErrors($errorsList = null): string
    {
        if (!$errorsList) {
            $errorsList = $this->getErrors();
        }

        $errors[] = '[' . $this->tag . ']';
        foreach ($errorsList as $error) {
            $errors[] = nl2br(implode(' ', $error));
        }

        return implode('<br>', $errors);
    }

    /**
     * @inheritDoc
     */
    protected function predefinedGutenbergFields(): array
    {
        return [
            'hr' => $this->fieldHr(),
            'divider' => $this->fieldHr(),
        ];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function prepareEndpoint(string $endpoint): string
    {
        return str_replace(
            [
                '${locale}',
            ],
            [
                LanguageHelper::dashboardLocale(),
            ],
            $endpoint
        );
    }

    /**
     * Создаем модель шорткода из строки
     * @example [shortcode_name attr="value"]
     * @param string $shortcode
     * @return self|null
     */
    public static function createFromString(string $shortcode): ?self
    {
        $tag = static::getTagFromString($shortcode);
        if ($tag && in_array($tag, static::shortcodeTags(), true)) {
            $model = self::getInstance(true);
            $model->tag = $tag;
            $shortcodeParams = explode(' ', trim($shortcode, '[]'), 2);
            [, $attributeListAsString] = $shortcodeParams;
            $attributesListAsArray = shortcode_parse_atts($attributeListAsString);

            $model->attributes = $attributesListAsArray;

            return $model;
        }
        return null;
    }

    /**
     * Список ассетов шорткода
     * @return Travelpayouts\components\assets\AssetEntry[]
     */
    public function getAssets(): array
    {
        return [];
    }

    /**
     * Регистрируем ассеты шорткода
     * @return void
     */
    public function registerAssets()
    {
        foreach ($this->getAssets() as $assetEntry) {
            $assetEntry->enqueueStyle()->enqueueScript();
        }
    }

    /**
     * Получаем название шорткода из строки
     * @example [shortcode_name attr="value"] => shortcode_name
     * @param string $shortcode
     * @return string|null
     */
    public static function getTagFromString(string $shortcode): ?string
    {
        $shortcode = trim($shortcode, '[]');
        $shortcode = explode(' ', $shortcode, 2);
        [$tag] = $shortcode;
        return is_string($tag) ? $tag : null;
    }
}

