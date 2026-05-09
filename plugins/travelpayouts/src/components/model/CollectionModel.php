<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\model;

use Exception;
use Travelpayouts\components\InjectedModel;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class CollectionModel
 * @package Travelpayouts\components\model
 * @property-read array $data
 * @property-read int $totalItemsCount
 * @property int $id
 */
abstract class CollectionModel extends InjectedModel
{
	protected $primaryKey = 'id';
	/**
	 * @var mixed[]
	 */
	private $_data;

	/**
	 * @var int|null
	 */
	private $_increment;

    private $_id;
    /**
     * @var array|null
     */
    private $_oldAttributes;
    /**
     * @var boolean
     */
    protected $allowSaveWithId;

	public function rules()
	{
		return [
			[['id'], 'number'],
		];
	}

	/**
	 * Возвращаем содержимое коллекции
	 * @return array
	 */
	abstract protected function getCollection();

	public function setId($value)
	{
		$this->_id = (int)$value;
	}

	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Сохраняем изменения в виде json представления данных
	 * @param string $value
	 * @return bool
	 */
	abstract protected function setCollection($value);

	public function save($runValidation = true)
	{
		if (($runValidation && !$this->validate()) || !$this->beforeSave()) {
			return false;
		}

		try {
			return $this->getIsNewRecord() ? $this->saveInternal() : $this->updateInternal();
		} catch (Exception $e) {
			return false;
		}
	}

    public function saveWithID($id)
    {
        $newItem = array_merge($this->assignPrimaryKeyToItem($this->attributes), ['id' => $id]);
        $this->_id = $this->getIncrement();
        $this->setIncrement($this->getIncrement() + 1);
        return $this->setData(array_merge($this->getData(), [
            $newItem,
        ]));
    }

    /**
     * Создаем запись
     * @return bool
     * @throws Exception
     */
    protected function saveInternal()
    {
        if($this->allowSaveWithId && $this->_id && !$this->findByPrimaryKey($this->_id)){
            $this->setIncrement($this->_id);
        }

        $newItem = $this->assignPrimaryKeyToItem($this->attributes);
        $this->_id = $this->getIncrement();
        $this->setIncrement($this->getIncrement() + 1);
        return $this->setData(array_merge($this->getData(), [
            $newItem,
        ]));
    }

	public function update($runValidation = true)
	{
		if ($runValidation && !$this->validate()) {
			return false;
		}
		try {
			return $this->updateInternal();
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Обновляем запись
	 * @return bool
	 * @throws Exception
	 */
	protected function updateInternal()
	{
		$result = [];
		foreach ($this->getData() as $item) {
			$itemId = isset($item[$this->primaryKey])
				? $item[$this->primaryKey]
				: null;

			if ($itemId !== null) {
				if ((int)$itemId !== (int)$this->id) {
					$result[] = $item;
				} else {
					$result[] = $this->assignPrimaryKeyToItem($this->attributes, (int)$this->id);
				}
			}
		}
		return $this->setData($result);
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		if (!$this->getIsNewRecord()) {
			try {
				return $this->deleteAllByPk([$this->id]);
			} catch (Exception $e) {
				return false;
			}
		}
		return false;
	}

	/**
	 * Удаляем записи по id
	 * @param array $idList
	 * @return bool
	 * @throws Exception
	 */
	public function deleteAllByPk($idList)
	{
		$data = $this->getData();

		$indexList = array_map(function ($id) {
			return $this->findKeyByValue('id', (int)$id);
		}, $idList);
		$indexList = array_filter($indexList, function ($index) {
			return is_int($index);
		});
		if (!empty($indexList)) {
			$filteredData = array_filter($data, static function ($itemPrimaryKey) use ($indexList) {
				return !in_array($itemPrimaryKey, $indexList, true);
			}, ARRAY_FILTER_USE_KEY);
			return $this->setData($filteredData);
		}
		return false;
	}

	/**
	 * Получаем количество элементов
	 * @return int
	 */
	public function getTotalItemsCount()
	{
		return count($this->getData());
	}

	/**
	 * Получаем содержимое коллекции
	 * @return array|mixed|mixed[]
	 */
	protected function getData()
	{
		if (!$this->_data) {
			$collection = $this->getCollection();
			if (is_array($collection) && isset($collection['data'])) {
				$this->_data = $collection['data'];
			} elseif (ArrayHelper::isIndexed($collection)) {
				$this->_data = $collection;
			} else {
				$this->_data = [];
			}
		}
		return $this->_data;
	}

	/**
	 * @param int $value
	 * @return CollectionModel|null
	 */
	public function findByPrimaryKey($value)
	{
		return $this->findByColumnValue('id', (int)$value);
	}

	/**
	 * @param $column - колонка для поиска
	 * @param $value - значение
	 * @return CollectionModel|null
	 * @see self::findKeyByValue()
	 */
	public function findByColumnValue($column, $value)
	{
		$key = $this->findKeyByValue($column, $value);
		$data = $this->getData();
		if ($key !== false && isset($data[$key])) {
			return $this->getModel($data[$key]);
		}
		return null;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	private function getModel($data)
	{
		$model = new static($data);
		$model->setOldAttributes($data);
		return $model;
	}

	/**
	 * Добавляем primaryKey к аттрибутам и возвращаем
	 * @param $attributes
	 * @param null $primaryKey
	 * @return array
	 */
	protected function assignPrimaryKeyToItem($attributes, $primaryKey = null)
	{
		return array_merge($attributes, [
			$this->primaryKey => $primaryKey
				?: $this->getIncrement(),
		]);
	}

	protected function getIncrement()
	{
		if (!$this->_increment) {
			$collection = $this->getCollection();
			if (is_array($collection) && ArrayHelper::isAssociative($collection) && isset($collection['increment'])) {
				$this->_increment = (int)$collection['increment'];
			} else {
				$this->_increment = 1;
			}
		}
		return $this->_increment;
	}

	private function setIncrement($value)
	{
		$this->_increment = $value;
	}

	private function setData($value)
	{
		if (is_array($value) && ArrayHelper::isIndexed($value)) {
			$data = json_encode([
				'increment' => $this->getIncrement(),
				'data' => array_values($value),
			]);
			$this->_data = $value;
			$result = $this->setCollection($data);
			if (!is_bool($result)) {
				$calledClass = get_called_class();
				throw new Exception("$calledClass::setCollection method must return boolean");
			}
			return true;
		}
		throw new Exception('Value must be an indexed array');
	}

	/**
	 * @param $column - колонка для поиска
	 * @param $value - значение
	 * @return false|int
	 */
	protected function findKeyByValue($column, $value)
	{
		return array_search($value, array_column($this->getData(), $column), false);
	}

	/**
	 * @return static[]
	 */
	public function findAll()
	{
		$data = $this->getData();
		$result = [];
		foreach ($data as $item) {
			$result[] = $this->getModel($item);
		}
		return $result;
	}

	/**
	 * @param int $primaryKey
	 * @return static|null
	 */
	public function findByPk($primaryKey)
	{
		return $this->findByPrimaryKey((int)$primaryKey);
	}

	public function fields()
	{
		return array_merge(parent::fields(), ['id']);
	}

	public function setOldAttributes($values)
	{
		$this->_oldAttributes = $values;
	}

	/**
	 * @return bool
	 */
	public function getIsNewRecord()
	{
		return $this->_oldAttributes === null;
	}

    public function beforeSave()
    {
        return true;
    }

    public function clearCollection()
    {
        $this->setIncrement(1);
        $this->setData([]);
    }

    /**
     * @param bool $allowSaveWithId
     * @return CollectionModel
     */
    public function setAllowSaveWithId($allowSaveWithId)
    {
        if (is_bool($allowSaveWithId)) {
            $this->allowSaveWithId = $allowSaveWithId;
        }
        return $this;
    }
}
