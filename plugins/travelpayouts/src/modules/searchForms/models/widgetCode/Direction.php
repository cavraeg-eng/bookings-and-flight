<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models\widgetCode;

class Direction extends \Travelpayouts\components\InjectedModel
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
	public $iata;

	public function rules()
	{
		return [
			[['name', 'iata'], 'string'],
			[
				['iata'],
				'string',
				'min' => 3,
				'max' => 3,
			],
		];
	}

	public function setCode($value)
	{
		if (is_string($value)) {
			$this->iata = $value;
		}
	}
}
