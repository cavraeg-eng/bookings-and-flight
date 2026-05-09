<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\Vendor\Glook\YiiGrid\DataView\GridView;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\grid\columns\GridColumn;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\InjectedModel;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\settings\SettingsForm;

class GridBuilder extends InjectedModel
{
    /**
     * @var TableShortcode
     */
    public $shortcodeModel;
    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    public function init()
    {
        if (!$this->shortcodeModel) {
            throw new InvalidConfigException(get_class($this) . ': You need to pass shortcodeModel');
        }
    }

    public function run()
    {
        if (!$this->shortcodeModel->getDataProvider()->count) {
            return $this->renderEmptyText();
        }

        $content = implode("\n", [
            $this->renderDebugData(),
            $this->renderTitle(),
            $this->renderSubtitle(),
            $this->renderGrid(),
        ]);
        return $this->getWrapper($content);
    }

    /**
     * @return array|null
     */
    protected function getDebugData(): ?array
    {

        $shortcodeModel = $this->shortcodeModel;
        return StringHelper::toBoolean($shortcodeModel->debug)
            ? [
                'shortcodeName' => $shortcodeModel->tag,
                'shortcodeAttributes' => array_filter($shortcodeModel->toArray($shortcodeModel->safe_attributes())),
            ]
            : null;
    }

    protected function getWrapper(string $content): string
    {
        $shortcodeModel = $this->shortcodeModel;
        $htmlOptions = [
            'class' => HtmlHelper::classNames([
                'travel',
                'tp-widget-table-container',
                $shortcodeModel->tableWrapperClassName,
                StringHelper::toBoolean($shortcodeModel->scroll) ? 'tp-widget-table--scroll' : null,
                $shortcodeModel->theme,
            ]),
        ];

        if (TRAVELPAYOUTS_DEBUG) {
            $htmlOptions['data-shortcode-tag'] = $shortcodeModel->tag;
        }

        $onLoadScript = $this->globalSettings->table_load_event;

        if ($onLoadScript) {
            $htmlOptions = array_merge($htmlOptions,
                [
                    'data-onload' => $onLoadScript,
                ]);
        }
        return HtmlHelper::tag(
            'div',
            $htmlOptions,
            $content
        );
    }

    /**
     * Оборачиваем debug данные в pre и отдаем
     * @return string
     */
    protected function renderDebugData(): string
    {
        $debugData = $this->getDebugData();
        return $debugData
            ?
            HtmlHelper::tagArrayContent('pre', [], print_r($this->getDebugData(), true))
            : '';
    }

    protected function renderTitle(): string
    {
        $shortcodeModel = $this->shortcodeModel;
        $title = $shortcodeModel->getGridTitle();
        if (empty($title) || $shortcodeModel->getHideTitle()) {
            return '';
        }
        // получаем конфигуратор заголовка, если указан
        $customTitleConfig = $this->shortcodeModel->getCustomGridTitleConfig();
        $customTitleHtmlProps = $customTitleConfig ? $customTitleConfig->getHtmlOptions() : [];
        $section = $shortcodeModel->section;

        $titleTag = $section->title_tag ?? 'H3';
        // сливаем опции из GridTitleStyleConfig, если существуют
        $attributes = array_merge([
            'class' => 'tp-widget-table-title',
        ], $customTitleHtmlProps);
        return HtmlHelper::tag($titleTag, $attributes, $title);
    }

    protected function renderSubtitle(): string
    {
        $subtitle = $this->shortcodeModel->getGridSubtitle();
        return $subtitle && is_string($subtitle) ? HtmlHelper::tag(
            'div',
            ['class' => 'tp-widget-table-subtitle'],
            $subtitle
        ) : '';
    }

    /**
     * @return string
     */
    public function renderGrid(): string
    {
        $shortcodeModel = $this->shortcodeModel;
        $section = $shortcodeModel->section;
        $dataProvider = $shortcodeModel->getDataProvider();

        $gridConfig = [
            'dataProvider' => $dataProvider,
            'columns' => $this->getGridColumns(),
            'options'=>  ['class' => 'tp-widget-table-grid'],
            'tableOptions' => [
                'class' => 'tp-widget-table',
                'data-options' => \json_encode(
                    [
                        'showPagination' => $shortcodeModel->getPaginate(),
                        'pageSize' => $section->getPaginationSize(),
                        'sortBy' => $this->getSortFieldIndex(),
                        'sortOrder' => 'asc',
                    ]
                ),
            ],
            'emptyText' => '',
        ];
        return (new GridView(
            ArrayHelper::mergeRecursive($gridConfig, $shortcodeModel->gridOptions())
        ))->run();
    }

    /**
     * Определяем индекс сортируемого поля
     * @return int
     */
    protected function getSortFieldIndex(): int
    {
        $section = $this->shortcodeModel->section;
        $gridColumns = $section->fieldColumns()->getTableShortcodeColumns($this->shortcodeModel);
        $keyIndex = array_search($section->sort_by, $gridColumns, true);
        return is_numeric($keyIndex) ? $keyIndex : 0;
    }

    /**
     * Получаем приоритетность колонки
     * @param string $attribute
     * @return int
     */
    protected function getGridColumnPriority(string $attribute): int
    {
        $priorityList = $this->shortcodeModel->gridColumnsPriority();
        return array_key_exists($attribute, $priorityList)
            ? $priorityList[$attribute]
            : TableShortcode::MIN_PRIORITY;
    }

    protected function getGridColumns(): array
    {
        $columnOverrides = $this->shortcodeModel->gridColumns();
        $shortcodeModel = $this->shortcodeModel;
        $section = $shortcodeModel->section;
        $columns = [];

        foreach ($section->getEnabledColumns() as $attribute) {
            $columnProps = [
                'class' => GridColumn::class,
                'attribute' => $attribute,
                'headerOptions' => [
                    'class' => HtmlHelper::classNames([
                        $shortcodeModel->getDisableHeader() ? 'hidden' : null,
                    ]),
                    'data-priority' => -$this->getGridColumnPriority($attribute),
                ],
            ];
            /**
             * Объединяем значения columnProps и columnOverrides
             */
            if (isset($columnOverrides[$attribute])) {
                $columnProps = GridColumn::mergeOptions($columnProps, $columnOverrides[$attribute]);
            }

            // если не указан заголовок, то получаем его из модели
            if (!isset($columnProps['label'])) {
                $columnProps['label'] = $shortcodeModel->getColumnLabel($attribute);
            }

            $columns[] = $columnProps;
        }

        return $columns;
    }

    protected function renderEmptyText()
    {
        $shortcodeModel = $this->shortcodeModel;
        $dataProvider = $shortcodeModel->getDataProvider();
        if (!$dataProvider->count) {
            $gridOptions = $shortcodeModel->gridOptions();
            if (isset($gridOptions['emptyText'])) {
                return $gridOptions['emptyText'];
            }
        }
        return '';
    }
}
