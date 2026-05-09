<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components;

use Travelpayouts;
use Travelpayouts\components\section\fields\Sorter;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\ArrayHelper;

class ColumnSorter extends Sorter
{
    /**
     * @var BaseTableFields
     */
    protected $tableSection;

    /**
     * Список ключей включенных колонок
     * @var array
     */
    protected $_enabledColumns = [];

    public function init()
    {
        parent::init();
        $this->id = 'columns';
        $this->title = Travelpayouts::__('Table columns');
        $this->subtitle = Travelpayouts::__('We offer a readymade combination for such a table, but you can edit the number 
                of columns and their arrangement.');
        $this->columnsOptions = [
            'enabled' => [
                'label' => Travelpayouts::__('Visible'),
            ],
            'disabled' => [
                'label' => Travelpayouts::__('Hidden'),
            ],
        ];

        if (!$this->tableSection instanceof BaseTableFields) {
            throw new \RuntimeException('You need to pass tableSection');
        }
        $this->_enabledColumns = $this->tableSection->enabledColumns();
        $this->setColumnsFromArray($this->tableSection->columns);
    }

    protected function setColumnsFromArray($columnsNew = []): void
    {
        if (is_array($columnsNew) && isset($columnsNew['enabled']) && ArrayHelper::isAssociative($columnsNew['enabled'])) {
            $value = $columnsNew['enabled'];
            if (is_array($value) && Travelpayouts\helpers\ArrayHelper::isAssociative($value)) {
                $defaultColumnList = $this->getAll();
                // Убираем из массива все ключи, которых нет в списке всех доступных колонок
                $enabledColumns = array_filter(array_keys($value), static function ($key) use ($defaultColumnList) {
                    return in_array($key, $defaultColumnList, true);
                });
                $this->_enabledColumns = $enabledColumns;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), [
            'options' => [$this, 'getOptions'],
        ]);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'enabled' => $this->getDashboardColumnLabels($this->getEnabled()),
            'disabled' => $this->getDashboardColumnLabels($this->getDisabled()),
        ];
    }

    /**
     * Получаем список всех доступных колонок
     * @return array
     */
    public function getAll(): array
    {
        return array_merge($this->tableSection->enabledColumns(), $this->tableSection->disabledColumns());
    }

    /**
     * Получаем список включенных колонок
     * @return array
     */
    public function getEnabled(): array
    {
        return $this->_enabledColumns;
    }

    /**
     * Получаем отключенные колонки путем вычитания из всех доступных колонок включенных
     * @return array
     */
    public function getDisabled(): array
    {
        return array_diff($this->getAll(), $this->getEnabled());
    }

    /**
     * Получаем заголовок колонки для дешборда
     * @param $name
     * @return string|null
     */
    public function getDashboardColumnLabel($name): ?string
    {
        $labels = $this->tableSection->dashboardColumnLabels();
        return $labels[$name] ?? $name;
    }

    /**
     * Получаем список заголовков колонок для дешборда
     * @param array $names
     * @return array
     */
    public function getDashboardColumnLabels(array $names): array
    {
        $result = [];
        foreach ($names as $name) {
            $result[$name] = $this->getDashboardColumnLabel($name);
        }
        return $result;
    }

    public function getTableShortcodeColumns(TableShortcode $shortcodeModel): array
    {
        $sectionColumns = $this->getEnabled();
        $gridColumns = $shortcodeModel->gridColumns();
        $result = [];

        foreach ($sectionColumns as $columnId) {
            if (isset($gridColumns[$columnId]) && is_array($gridColumns[$columnId])) {
                $columnVisible = $gridColumns[$columnId]['visible'] ?? true;
                if ($columnVisible) {
                    $result[] = $columnId;
                }
            }
        }
        return $result;
    }
}