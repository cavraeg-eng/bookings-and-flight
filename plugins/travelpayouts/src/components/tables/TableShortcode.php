<?php

namespace Travelpayouts\components\tables;
use Travelpayouts\Vendor\Glook\YiiGrid\Data\ArrayDataProvider;
use Travelpayouts;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\arrayQuery\ArrayQuery;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\grid\GridBuilder;
use Travelpayouts\components\grid\GridTitleStyleConfig;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\shortcodes\ShortcodeModel;
use Travelpayouts\components\validators\ValidatorBooleanString;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\settings\SettingsForm;
use Travelpayouts\modules\tables\components\BaseTableFields;
use Travelpayouts\modules\tables\components\settings\CustomTableStylesSection;

/**
 * Class TableModel
 * @package Travelpayouts\src\components\tables
 */
abstract class TableShortcode extends ShortcodeModel
{
    public const MIN_PRIORITY = 1;
    public const MAX_PRIORITY = 100;

    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $button_title;
    /**
     * @var string
     */
    public $subid;
    /**
     * @var string
     */
    public $locale;
    /**
     * @var string
     */
    public $currency;

    /**
     * @var bool|string
     */
    public $paginate = true;

    /**
     * @var bool|string
     */
    public $disable_header = false;

    /**
     * @var bool|string
     */
    public $off_title = false;

    /**
     * @var string
     */
    public $tableWrapperClassName = '';
    /**
     * @var string
     */
    public $theme = 'default-theme';

    /**
     * @var boolean
     */
    public $debug = false;

    /**
     * @var bool
     */
    public $scroll = false;

    /**
     * @var BaseTableFields|null
     */
    public $section;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    /**
     * @Inject
     * @var Cache
     */
    protected $cache;

    /**
     * @var ArrayDataProvider
     */
    protected $_dataProvider;

    /**
     * @var string|null;
     */
    protected $_rawTitleText;

    public function init()
    {
        $this->currency = $this->globalSettings->currency;
        $this->locale = LanguageHelper::tableLocale();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['title', 'button_title', 'subid'], 'string'],
            [
                ['currency'],
                'string',
                'length' => 3,
            ],
            [
                ['locale'],
                'in',
                'range' => array_keys(Travelpayouts::getInstance()->translator->getLocaleNames()),
            ],
            [['currency'], 'in', 'range' => array_keys(ReduxOptions::table_widget_currencies())],
            [['theme'], 'in', 'range' => array_keys(self::availableThemes())],
            [['debug', 'scroll', 'off_title', 'paginate', 'disable_header'], ValidatorBooleanString::class],
        ]);
    }

    public function render()
    {
        if (!$this->validate()) {
            return $this->renderErrors();
        }
        $this->registerAssets();
        return $this->renderGrid();
    }

    public function getAssets(): array
    {
        return [Travelpayouts::getInstance()->assets->getAssetByName('publicTables')];
    }

    /**
     * Маркер таблицы по умолчанию - имя шорткода
     * @return string
     */
    public function linkMarker()
    {
        return $this->tag;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function fields()
    {
        $fields = array_merge(parent::fields(), [
            'paginate',
            'off_title',
            'disable_header',
        ]);
        if ($this->scenario === self::SCENARIO_GENERATE_SHORTCODE) {
            return $this->safe_attributes();
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function attribute_labels()
    {
        return array_merge(parent::attribute_labels(), [
            'title' => Travelpayouts::__('Alternate title'),
            'off_title' => Travelpayouts::__('Hide title'),
            'button_title' => Travelpayouts::__('Alternate button title'),
            'subid' => Travelpayouts::__('Sub ID'),
            'paginate' => Travelpayouts::__('Paginate'),
            'disable_header' => Travelpayouts::__('Disable table header'),
            'locale' => Travelpayouts::__('Table language'),
            'currency' => Travelpayouts::__('Currency'),
            'origin' => Travelpayouts::__('Origin'),
            'destination' => Travelpayouts::__('Destination'),
        ]);
    }

    /**
     * @return bool
     */
    public function getHideTitle(): bool
    {
        return StringHelper::toBoolean($this->off_title);
    }

    /**
     * @return bool
     */
    public function getPaginate(): bool
    {
        return StringHelper::toBoolean($this->paginate);
    }

    /**
     * @return bool
     */
    public function getDisableHeader(): bool
    {
        return StringHelper::toBoolean($this->disable_header);
    }

    /**
     * @inheritdoc
     */
    public static function render_shortcode_static($attributes = [], $content = null, $tag = '')
    {
        $shortcodeModel = new static();
        $shortcodeModel->tag = $tag;
        $shortcodeModel->attributes = $attributes;
        return $shortcodeModel->render();
    }

    /**
     * Список значений использующихся при генерации заголовка таблицы
     * @return string[] | callable(string):string
     */
    protected function titleVariables(): array
    {
        return [];
    }

    /**
     * Получаем ключи из getTitleVariables()
     * @return array
     */
    public function getTableTitleVariableKeys(): array
    {
        return array_keys($this->titleVariables());
    }

    /**
     * Формируем значения из availableTitleTags
     * @return string[]
     */
    protected function prepareTableTitleTags(): array
    {
        $titleTags = $this->titleVariables();
        $result = [];
        foreach ($titleTags as $key => $titleTag) {
            if (is_callable($titleTag)) {
                $calculatedValue = $titleTag($key);
                if (is_string($calculatedValue) || is_numeric($calculatedValue)) {
                    $result[$key] = $calculatedValue;
                }
            } elseif (is_string($titleTag)) {
                $result[$key] = $titleTag;
            } elseif (is_numeric($titleTag)) {
                $result[$key] = (string)$titleTag;
            }
        }
        return $result;
    }

    /**
     * Ищем исходный текст заголовка таблицы начиная с аттрибута title
     * и заканчивая предустановленным значением из titlePlaceholder()
     * @return string|null
     */
    protected function getRawTableTitleText(): ?string
    {
        if (!$this->_rawTitleText) {
            $titleList = [
                $this->title,
            ];
            $section = $this->section;
            if ($section instanceof BaseTableFields) {
                $titleList = array_merge(
                    $titleList, [
                        $section->title,
                        $section->titlePlaceholder($this->locale),
                    ]
                );
            }
            $this->_rawTitleText = ArrayHelper::find($titleList, [
                $this,
                'isStringIsNotEmpty',
            ]);
        }
        return $this->_rawTitleText;
    }

    /**
     * @param string $value
     * @return void
     */
    protected function setRawTitleText(string $value): void
    {
        $this->_rawTitleText = $value;
    }

    /**
     * @return string|null
     */
    public function getRawButtonTitleText(): ?string
    {
        $titleList = [
            $this->button_title,
        ];

        $section = $this->section;
        if ($section instanceof BaseTableFields) {
            $titleList = array_merge(
                $titleList, [
                    $section->button_title,
                    $section->buttonPlaceholder($this->locale),
                ]
            );
        }
        return ArrayHelper::find($titleList, [
            $this,
            'isStringIsNotEmpty',
        ]);
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isStringIsNotEmpty($value): bool
    {
        return is_string($value) && $value !== '';
    }

    /**
     * Отдаем заголовок таблицы
     * @return string
     */
    public function getGridTitle(): string
    {
        $titleTags = $this->prepareTableTitleTags();
        $rawTitleText = $this->getRawTableTitleText();
        return is_string($rawTitleText) ? StringHelper::formatMessage($rawTitleText, $titleTags) : '';
    }

    /**
     * Отдаем подзаголовок таблицы
     * @return string|null
     */
    public function getGridSubtitle(): ?string
    {
        return null;
    }

    /**
     * Массив содержащий приоритетность колонок
     * @return array
     */
    public function gridColumnsPriority(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function gridColumns(): array
    {
        return [];
    }

    /**
     * Создаем ArrayDataProvider предварительно фильтруя коллекцию
     */
    public function getDataProvider(): ArrayDataProvider
    {
        if (!$this->_dataProvider) {
            $allModels = $this->getCollection();
            $this->_dataProvider = new ArrayDataProvider([
                'allModels' => $this->filterCollection($allModels),
            ]);
        }
        return $this->_dataProvider;
    }

    /**
     * @return string
     */
    public function renderGrid(): string
    {
        try {
            return (new GridBuilder([
                'shortcodeModel' => $this,
            ]))->run();
        } catch (\Exception $exception) {
            $this->add_error('tag', $exception->getMessage());
            return $this->renderErrors();
        }
    }

    /**
     * Дополнительные параметры для грида
     * @return array
     */
    public function gridOptions(): array
    {
        return [];
    }

    /**
     * Заголовки колонок
     * @return array
     */
    public function columnLabels(): array
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return string|null
     */
    public function getColumnLabel(string $attribute): ?string
    {
        return $this->columnLabels()[$attribute] ?? null;
    }

    /**
     * Возвращаем список элементов, которые будут отображены в таблице
     * @return array
     */
    protected function getCollection(): array
    {
        return [];
    }

    /**
     * Фильтруем коллекцию
     * @param array $collection
     * @return array
     */
    protected function filterCollection(array $collection): array
    {
        try {
            $query = new ArrayQuery();
            $query->from($collection);
            $this->filters($query);
            return $query->all();
        } catch (\Exception $exception) {
            if (TRAVELPAYOUTS_DEBUG) {
                $this->add_error('tag', "got an error while trying to filter collection. Return unfiltered results \n" . $exception->getMessage());
                echo $this->renderErrors();
            }

            return $collection;
        }

    }

    /**
     * Список фильтров применяемых к элементам полученным из функции getCollection()
     * @param ArrayQuery $query
     * @return void
     */
    protected function filters(ArrayQuery $query): void
    {
    }

    /**
     * Отдаем конфигуратор стилей заголовка таблицы
     * @return GridTitleStyleConfig|null
     */
    public function getCustomGridTitleConfig(): ?GridTitleStyleConfig
    {
        return null;
    }

    /**
     * Список доступных тем для таблицы
     * @return array
     */
    public static function availableThemes(): array
    {
        return [
            'default-theme' => Travelpayouts::__('Default theme'),
            'red-button-table' => Travelpayouts::__('Bright theme with a red button'),
            'blue-table' => Travelpayouts::__('Light theme with a blue button'),
            'grey-salad-table' => Travelpayouts::__('Light theme with a light green button'),
            'purple-table' => Travelpayouts::__('Light theme with a purple button'),
            'black-and-yellow-table' => Travelpayouts::__('Dark theme with a yellow button'),
            'dark-and-rainbow' => Travelpayouts::__('Dark theme with a coral button'),
            'light-and-plum-table' => Travelpayouts::__('Light theme with a plum search column'),
            'light-yellow-and-darkgray' => Travelpayouts::__('Light theme with a dark search column'),
            'mint-table' => Travelpayouts::__('Light theme with a mint button'),
            CustomTableStylesSection::CUSTOM_THEME => Travelpayouts::__('Custom theme styles'),
        ];
    }

    public function getTitleVariableKeys(): array
    {
        return array_keys($this->titleVariables());
    }

    public function titleVariableLabels(): array
    {
        return [];
    }

    public function buttonVariableLabels(): array
    {
        return [
            'price' => Travelpayouts::__('Price'),
        ];

    }

    public function buttonVariables(): array
    {
        return [];
    }

    public function getButtonVariables(): array
    {
        $labels = $this->buttonVariableLabels();
        $result = [];

        foreach (array_keys($this->buttonVariables()) as $variableName) {
            $result[$variableName] = $labels[$variableName] ?? $variableName;
        }
        return $result;

    }

    public function getTitlesVariables(): array
    {
        $labels = $this->titleVariableLabels();
        $result = [];

        foreach (array_keys($this->titleVariables()) as $variableName) {
            $result[$variableName] = $labels[$variableName] ?? $variableName;
        }
        return $result;
    }

    protected function predefinedGutenbergFields(): array
    {
        return array_merge(parent::predefinedGutenbergFields(), [
            'subid' => $this->fieldInput(),
            'button_title' => $this->fieldInputWithVariablesInDescription()
                ->setVariables($this->getButtonVariables()),
            'title' => $this->fieldInputWithVariablesInDescription()
                ->setVariables($this->getTitlesVariables()),
            'off_title' => $this->fieldCheckbox(),
            'currency' => $this->fieldSelect()
                ->setOptions(ReduxOptions::table_widget_currencies())->setPlaceholder(Travelpayouts::__('Default')),
            'locale' => $this->fieldSelect()
                ->setOptions(Travelpayouts::getInstance()->translator->getLocaleNames())
                ->setPlaceholder(Travelpayouts::__('Default')),
            'paginate' => $this->fieldCheckbox(),
            'disable_header' => $this->fieldCheckbox(),
        ]);
    }

}
