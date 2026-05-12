<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\notices;

use ReflectionClass;
use ReflectionProperty;
use Travelpayouts\components\interfaces\IBuilderResult;

class Notice implements IBuilderResult
{
    const NOTICE_TYPE_WARNING = 'warning';
    const NOTICE_TYPE_ERROR = 'error';
    const NOTICE_TYPE_SUCCESS = 'success';
    const NOTICE_TYPE_INFO = 'info';

    public $name;
    public $type;
    public $title;
    public $description;
    public $allowClose = false;
    public $buttons = [];

    /**
     * @param string $name
     * @return static
     */
    public static function create($name)
    {
        return new static($name);
    }

    /**
     * Notice constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setType($value)
	{
		if (is_string($value)) {
			$this->type = $value;
		}

		return $this;
	}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setTitle($value)
	{
		if (is_string($value)) {
			$this->title = $value;
		}
		return $this;
	}

	/**
	 * @param string $value
	 * @return $this
     */
    public function setDescription($value)
    {
        if (is_string($value)) {
            $this->description = $value;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setCloseable()
    {
        $this->allowClose = true;
        return $this;
    }

    public function addButton(NoticeButton $value)
    {
        $this->buttons = array_merge($this->buttons, [$value->getResult()]);
        return $this;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        $class = new ReflectionClass($this);
        $data = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $attribute = $property->getName();
                $data[$attribute] = $this->$attribute;
            }
        }
        return $data;
    }
}
