<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid\columns;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\settings\SettingsForm;

abstract class ColumnButton extends GridColumn
{
    /**
     * @var string[]
     */
    public $contentOptions = [
        'class' => 'button-content',
    ];

    /**
     * @var array
     */
    protected $buttonVariables = [];

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    protected $urlTarget;
    protected $urlRel;

    /**
     * @var string|null|callable
     */
    protected $sortProperty;

    public function init()
    {
        HtmlHelper::addCssClass($this->headerOptions, HtmlHelper::classNames([
            'button-content',
        ]));

        $this->headerOptions = array_merge($this->headerOptions, [
            'data-priority' => -100,
        ]);

        if (StringHelper::toBoolean($this->globalSettings->target_url)) {
            $this->urlTarget = '_blank';
        }

        if (StringHelper::toBoolean($this->globalSettings->nofollow)) {
            $this->urlRel = 'nofollow';
        }
    }

    /**
     * @inheritDoc
     */
    protected function getSortOrderValue($model, $key, int $index)
    {
        $buttonVariables = $this->resolveButtonVariablesByModel($model);
        if ($this->sortProperty && isset($buttonVariables['price'])) {
            $value = null;

            $sortingProperty = $this->sortProperty;
            if (is_string($sortingProperty)) {
                $value = $model->{$sortingProperty};
            }
            /**
             * Когда принимаем callable
             */
            if (is_array($sortingProperty) && count($sortingProperty) === 2) {
                [, $sortingProperty] = $sortingProperty;
                $value = $model->{$sortingProperty}();
            }
            return $value !== null ? (int)$value : 99999999;
        }
        return null;
    }

    /**
     * @param string $href
     * @return array
     */
    protected function getHtmlOptions(string $href): array
    {
        return array_filter([
            'href' => UrlHelper::getInstance()->getUrl($href),
            'target' => $this->urlTarget,
            'rel' => $this->urlRel,
            'class' => 'travelpayouts-table-button',
        ]);
    }

    /**
     * @param $model
     * @return array
     */
    public function getButtonVariablesByModel($model): array
    {
        $buttonVariables = $this->resolveButtonVariablesByModel($model);
        if (is_array($buttonVariables)) {
            $result = [];
            foreach ($buttonVariables as $key => $item) {
                if (is_callable($item)) {
                    $result[$key] = $item($model);
                } else {
                    $result[$key] = $item;
                }
            }
            return $result;
        }
        return [];
    }

    protected function resolveButtonVariablesByModel($model)
    {
        $buttonVariables = $this->buttonVariables;
        return is_callable($buttonVariables) ? $buttonVariables($model) : $buttonVariables;
    }

    /**
     * @param $buttonTitle
     * @param $model
     * @return string
     */
    protected function getButtonLabel($buttonTitle, $model): ?string
    {

        return is_string($buttonTitle) ? StringHelper::formatMessage($buttonTitle, $this->getButtonVariablesByModel($model)) : null;
    }

    abstract public function getButtonUrl($model): ?string;

    /**
     * @inheritDoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->value;
        $href = $this->getButtonUrl($model);
        $buttonLabel = $this->getButtonLabel($value, $model);
        return $buttonLabel && $href ? HtmlHelper::tag('a', $this->getHtmlOptions($href), $buttonLabel) : null;
    }

}
