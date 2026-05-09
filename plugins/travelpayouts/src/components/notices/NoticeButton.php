<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\notices;

use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\interfaces\IBuilderResult;

class NoticeButton implements IBuilderResult
{
	const BUTTON_TYPE_PRIMARY = 'primary';
	const BUTTON_TYPE_SECONDARY = 'secondary';

	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $url = '#';

	/**
	 * @var string
	 */
	public $className;
	/**
	 * @var bool
	 */
	public $inNewWindow = false;
	public $attributes = [];

	/**
	 * @param string $text
	 * @return static
	 */
	public static function create($text)
	{
		return new static($text);
	}

	/**
	 * Notice constructor.
	 * @param string $text
	 */
	public function __construct($text)
	{
		if (is_string($text)) {
			$this->text = $text;
		}
	}

	public function setType($value)
	{
		if (is_string($value)) {
			$this->type = $value;
		}
		return $this;

	}

	public function setClassName($value)
	{
		if (is_string($value)) {
			$this->className = $value;
		}
		return $this;

	}

	public function setUrl($value)
	{
		if (is_string($value)) {
			$this->url = $value;
		}
		return $this;
	}

	public function openInNewWindow()
	{
		$this->inNewWindow = true;
		return $this;
	}

	/**
	 * @param $values
	 * @return NoticeButton
	 */
	public function setAttributes($values)
	{
		if (is_array($this->attributes)) {
			$this->attributes = $values;
		}
		return $this;
	}

	protected function getClassName()
	{
		$classNames = [
			'travelpayouts-notice-btn',
			$this->className,
			$this->type ? "travelpayouts-notice-btn--{$this->type}" : null,
		];

		return implode(' ', array_filter($classNames));
	}

	public function getResult()
	{
		return HtmlHelper::tag('a', array_filter(
			array_merge(
				[
					'class' => $this->getClassName(),
					'target' => $this->inNewWindow ? '_blank' : null,
					'href' => $this->url,
				], $this->attributes
			)
		), $this->text);
	}

}
