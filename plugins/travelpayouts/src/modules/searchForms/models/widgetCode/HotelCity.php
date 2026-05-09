<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models\widgetCode;

use Travelpayouts;
use Travelpayouts\components\InjectedModel;

class HotelCity extends InjectedModel
{

    const HOTEL_REGEXP = '/^(?<name>[^,]+),\s?(?<location>.+),\s?(?<search_id>\d+),\s?(?<search_type>hotel),\s?(?<country_name>.+)$/';
    const CITY_REGEXP = '/^(?<name>.+),\s?(?<location>.+),\s?(?<hotels_count>\d+)?,\s?(?<search_id>\d+),\s?(?<search_type>city),\s?(?<country_name>.+)$/';

    public $name;
	public $location;
	public $hotels_count;
	public $search_id;
	public $search_type;
	public $country_name;
    /**
     * @var string
     */
    public $query;


	public function rules()
	{
		return [
			[['country_name', 'location', 'name', 'search_type'], 'string'],
			[['hotels_count', 'search_id'], 'number'],
			[['country_name', 'location', 'name', 'search_type', 'search_id'], 'required'],
		];
	}

	public function attribute_labels()
	{
		return [
			'name' => Travelpayouts::__('Name'),
			'location' => Travelpayouts::__('Location'),
			'hotels_count' => Travelpayouts::__('Hotels Count'),
			'search_id' => Travelpayouts::__('Search Id'),
			'search_type' => Travelpayouts::__('Search Type'),
			'country_name' => Travelpayouts::__('Country Name'),
		];
	}

	public function getHotelsCount()
	{
		return $this->hotels_count ? (int)$this->hotels_count : null;
	}

	public function getSearchId()
	{
		return $this->search_id ? (int)$this->search_id : null;
	}

	/**
	 * Парсим аттрибуты модели из строки и создаем новую модель
	 * @param $value
	 * @return HotelCity
	 */
	public static function createFromString($value)
	{
		$model = new self();
        $model->query = $value;
		if (preg_match(self::HOTEL_REGEXP, $value, $hotelAttributes)) {
			$model->attributes = $hotelAttributes;
		} elseif (preg_match(self::CITY_REGEXP, $value, $cityAttributes)) {
            $model->attributes = $cityAttributes;
		}
		return $model;
	}

}
