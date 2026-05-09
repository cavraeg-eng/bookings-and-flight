<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use InvalidArgumentException;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\section\fields\Accordion;
use Travelpayouts\components\section\ReduxFieldResolverTrait;
use Travelpayouts\components\section\ReduxFieldsTrait;
use Travelpayouts\includes\ReduxConfigurator;
use Travelpayouts\traits\GetterSetterTrait;

/**
 * Class Base
 * @package Travelpayouts\admin\redux\base
 * @property-read string $id
 * @property-read string $optionPath
 * @property-read null|Base $parent
 */
abstract class Base extends BaseInjectedObject implements IModuleSectionFields
{
    use ReduxFieldsTrait;
    use ReduxFieldResolverTrait;
    use GetterSetterTrait;

    /**
     * @Inject
     * @var ReduxConfigurator
     */
    public $redux;
    /**
     * @var Base[]
     */
    private $_children = [];
    /**
     * @var Base
     * @see getParent()
     * @see setParent()
     */
    private $_parent;

    /**
     * @var string
     */
    protected $_optionPath;

    /**
     * @var array
     */
    protected $_optionData;

    /**
     * Активна ли секция/подсекция
     * В случае false регистрации секции/подсекции/полей не происходит
     * @return bool
     */
    public static function isActive(): bool
    {
        return true;
    }

    /**
     * Инициализируем базовый класс для Redux секции/подсекции/полей
     * В случае если указана переменная $parent появится возможность обратиться к
     * родительскому классу, а также в родительский класс будет добавлен текущий класс
     * как дочерний для последующего взаимодействия между ними
     * @param Base| null $parent
     * @param array $config
     */
    public function __construct($parent = null, $config = [])
    {
        if ($parent) {
            if (!$parent instanceof self) {
                throw new InvalidArgumentException('$parent must extends ' . self::class);
            }
            $this->setParent($parent);
        }
        parent::__construct($config);
    }

    public function init()
    {
        self::configure($this, $this->data->all());
    }

    /**
     * @return Base
     */
    protected function getParent(): ?Base
    {
        return $this->_parent;
    }

    /**
     * Устанавливаем родительский класс
     * @param Base $parent
     */
    final protected function setParent(Base $parent): void
    {
        if (!$this->_parent) {
            $this->_parent = $parent;
            // добавляем родителю дочерний класс
            $this->_parent->setChildren($this);
        }
    }

    /**
     * @param Base $value
     */
    public function setChildren($value): void
    {
        $this->_children = array_merge($this->_children, [$value]);
    }

    /**
     * @return Base[]
     */
    final protected function getChildren(): array
    {
        return $this->_children;
    }

    /**
     * @inheritdoc
     */
    final public function getOptionPath(): string
    {
        if (!$this->_optionPath) {
            $this->_optionPath = $this->parent
                ? $this->parent->getOptionPath() . '_' . $this->optionPath()
                : $this->optionPath();
        }
        return $this->_optionPath;
    }

    /**
     * Возвращаем путь данной опции
     * @return string
     * @see optionPath()
     */
    final public function getId(): string
    {
        return $this->optionPath();
    }
    /**
     * Получаем отфильтрованные данные из redux опции
     * @return array
     */
    final protected function getOptionPathData(): array
    {
        if (!$this->_optionData) {
            $options = $this->redux->get_options();
            $result = [];
            if (is_array($options) && !empty($options)) {
                $prefix = $this->getOptionPath();
                $fieldsWithPrefix = preg_grep('/^' . $prefix . '/', array_keys($options));
                foreach ($fieldsWithPrefix as $fieldName) {
                    $fieldNameWithoutPrefix = substr_replace($fieldName, '', 0, strlen($prefix) + 1);
                    $result[$fieldNameWithoutPrefix] = $options[$fieldName];
                }
            }
            $this->_optionData = $result;
        }
        return $this->_optionData;
    }

    /**
     * Добавляем префикс опции к id поля для корректной работы с TravelpayoutsSettingsFramework
     * @param array $fields
     * @return array
     */
    public function addPrefixToFields(array $fields): array
    {
        $prefix = $this->getOptionPath();
        $result = [];
        foreach ($fields as $field) {
            if (isset($field['id'])) {
                $field['id'] = $prefix . '_' . $field['id'];
            }
            $result[] = $field;
        }
        return $result;
    }

    protected function predefinedFields(): array
    {
        return [];
    }

    public function fieldAccordion(): Accordion
    {
        return (new Accordion())->setPredefinedFields($this->predefinedFields());
    }
}
