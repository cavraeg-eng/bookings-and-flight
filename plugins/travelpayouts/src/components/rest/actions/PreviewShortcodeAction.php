<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\actions;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\exceptions\HttpException;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\components\web\CheckAccessAction;
use Travelpayouts\frontend\PublicHooks;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\modules\links\components\BaseLinkShortcode;

/**
 * Class PreviewShortcodeAction
 * @package Travelpayouts\components\rest
 * Действие для рендеринга шорткода внутри гутенберга
 */
class PreviewShortcodeAction extends CheckAccessAction
{
    /**
     * @Inject()
     * @var PublicHooks
     */
    protected $publicHooks;

    /**
     * @var string[]
     */
    protected $shortcodeList = [];

    /**
     * @var ShortcodeModel[]
     */
    protected $shortcodeModels = [];

    /**
     * @var bool
     */
    protected $_isHandlerRegistered = false;

    public function init()
    {
        $this->shortcodeModels = $this->getShortcodeModels();
    }

    /**
     * @return ShortcodeModel[]
     */
    protected function getShortcodeModels(): array
    {
        $result = [];
        foreach ($this->shortcodeList as $shortcodeClass) {
            if (method_exists($shortcodeClass, 'getInstance') && $shortcodeClass::getInstance() instanceof ShortcodeModel) {
                $result[] = $shortcodeClass::getInstance();
            }
        }
        return $result;
    }

    public function run()
    {
        $shortcode = Travelpayouts::getInstance()->request->getInputParam('shortcode');
        if (!$shortcode) {
            throw new HttpException(500, 'Shortcode parameter is required');
        }

        $shortcodeModel = $this->findModelFromShortcodeList($shortcode);
        if ($shortcodeModel) {
            return [
                'html' => $this->renderShortcode($shortcodeModel),
            ];
        }

        throw new HttpException(404, 'Shortcode not found');
    }

    /**
     * @param string $shortcode
     * @return ShortcodeModel|null
     */
    protected function findModelFromShortcodeList(string $shortcode): ?ShortcodeModel
    {
        $shortcode = stripslashes($shortcode);
        $tag = ShortcodeModel::getTagFromString($shortcode);
        /** @var ShortcodeModel|null $foundShortcodeModel */
        $foundShortcodeModel = ArrayHelper::find($this->shortcodeModels, static function ($shortcodeModel) use ($tag) {
            return in_array($tag, $shortcodeModel->shortcodeTags(), true);
        });

        if ($foundShortcodeModel) {
            $shortcodeModel = $foundShortcodeModel::createFromString($shortcode);
            if ($shortcodeModel) {
                return $shortcodeModel;
            }
        }
        return null;
    }

    /**
     * @param ShortcodeModel $model
     * @return string
     */
    protected function renderShortcode(ShortcodeModel $model): string
    {
        return $this->renderAssets($model) . "\n" . $model->render();
    }

    /**
     * @param ShortcodeModel $model
     * @return string
     */
    protected function renderAssets(ShortcodeModel $model): string
    {
        ob_start();
        Travelpayouts::getInstance()->assets->getAssetByName('runtime')->enqueueScript();

        if ($model instanceof TableShortcode) {
            // Регистрируем jquery для таблиц
            wp_enqueue_script('jquery');
            // Выводим кастомные стили для таблиц
            $this->publicHooks->appendCustomTableStyles();
        }

        // Добавляем стили для ссылок
        if ($model instanceof BaseLinkShortcode) {
            $inlineLinkStyles = <<<CSS
  a { 
    color: #0085ff !important;
    font-family: "Roboto", sans-serif !important;
  }
CSS;
            wp_add_inline_style($this->getStylesHandlerName(), $inlineLinkStyles);
        }

        $model->registerAssets();
        wp_print_styles();
        wp_print_scripts();

        $output = ob_get_clean();
        return is_string($output) ? $output : '';
    }

    /**
     * Регистрируем стиль пустышку, чтобы можно было добавить инлайн стили
     * @return string
     */
    protected function getStylesHandlerName(): string
    {
        $shortcodeStylesHandleName = 'shortcodeStyles';

        if (!$this->_isHandlerRegistered) {
            wp_register_style($shortcodeStylesHandleName, false);
            wp_enqueue_style($shortcodeStylesHandleName);
            $this->_isHandlerRegistered = true;
        }
        return $shortcodeStylesHandleName;
    }
}