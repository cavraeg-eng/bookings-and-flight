<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models;

use Travelpayouts\components\Model;
use Travelpayouts\modules\searchForms\models\widgetCode\BestOffer;
use Travelpayouts\modules\searchForms\models\widgetCode\Direction;
use Travelpayouts\modules\searchForms\models\widgetCode\HotelCity;

/**
 * Class WidgetCode
 * @package Travelpayouts\modules\searchForms\models
 * @property BestOffer|null $best_offer
 * @property HotelCity|null $hotel
 * @property Direction|null $origin
 * @property Direction|null $destination
 */
class WidgetCode extends Model
{
	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var string
	 */
	public $handle;
	/**
	 * @var string
	 */
	public $widget_name;
	/**
	 * @var string
	 */
	public $form_type;
	/**
	 * @var string
	 */
	public $marker;
	/**
	 * @var string
	 */
	public $additional_marker;
	/**
	 * @var string
	 */
	public $locale;
	/**
	 * @var string
	 */
	public $currency;
	/**
	 * @var bool
	 */
	public $show_logo;
	/**
	 * @var bool
	 */
	public $show_hotels;
	/**
	 * @var string _blank|_self
	 */
	public $search_target;
	/**
	 * @var string avia|hotel
	 */
	public $active_tab;
	/**
	 * @var bool
	 */
	public $retargeting;

	// flights
	/**
	 * @var string
	 */
	public $search_host;
	/**
	 * @var string
	 */
	public $trip_class;
	/**
	 * @var string
	 */
	public $depart_date;
	/**
	 * @var string
	 */
	public $return_date;
	/**
	 * @var string
	 */
	public $avia_alt;

	/**
	 * @var string|null
	 */
	public $search_logo_host;
	// !flights

	// hotels
	/**
	 * @var string
	 */
	public $hotels_host;
	/**
	 * @var string
	 */
	public $hotel_alt;

	/**
	 * @var string
	 */
	public $check_in_date;
	/**
	 * @var string
	 */
	public $check_out_date;

	/**
	 * @var string
	 */
	public $hotels_type;

	/**
	 * @var string|null
	 */
	public $hotel_logo_host;
	/**
	 * @var string|null
	 */
	public $hotel_marker_format;
	/**
	 * @var string|null
	 */
	public $hotelscombined_marker;
	// !hotels

	/**
	 * @var bool
	 */
	public $no_track;
	/**
	 * @var bool
	 */
	public $powered_by;
	/**
	 * @var int
	 */
	public $source_id;
	/**
	 * @var array
	 */
	public $sources;
	/**
	 * @var array
	 */
	public $color_scheme;
	/**
	 * @var int
	 */
	public $width;
	/**
	 * @var int
	 */
	public $height;
	/**
	 * @var bool
	 */
	public $responsive;
	/**
	 * @var string
	 */
	public $border_radius;
	/**
	 * @var string
	 */
	public $sizes;
	/**
	 * @var BestOffer
	 */
	protected $bestOffer;
	/**
	 * @var HotelCity|null
	 */
	protected $_hotel;
	/**
	 * @var Direction|null
	 */
	private $_destination;
	/**
	 * @var Direction|null
	 */
	private $_origin;

	/**
	 * @param $value
	 */
	public function setBest_offer($value)
	{
		if (is_array($value)) {
			$this->bestOffer = new BestOffer($value);
		}
		if (!$value) {
			$this->bestOffer = $value;
		}
	}

	/**
	 * @return BestOffer|null
	 */
	public function getBest_offer()
	{
		return $this->bestOffer;
	}

	/**
	 * @param $value
	 */
	public function setHotel($value)
	{
		if (is_array($value)) {
			$this->_hotel = new HotelCity($value);
		}
		if ($value instanceof HotelCity) {
			$this->_hotel = $value;
		}
		if (!$value) {
			$this->_hotel = $value;
		}
	}

	/**
	 * @return HotelCity|null
	 */
	public function getHotel()
	{
		return $this->_hotel;
	}

	/**
	 * @param $value
	 */
	public function setOrigin($value)
	{
		if (is_array($value)) {
			$this->_origin = new Direction($value);
		}
		if ($value instanceof Direction) {
			$this->_origin = $value;
		}
		if (!$value) {
			$this->_origin = $value;
		}
	}

	/**
	 * @return Direction|null
	 */
	public function getOrigin()
	{
		return $this->_origin;
	}

	/**
	 * @param $value
	 */
	public function setDestination($value)
	{
		if (is_array($value)) {
			$this->_destination = new Direction($value);
		}
		if ($value instanceof Direction) {
			$this->_destination = $value;
		}
		if (!$value) {
			$this->_destination = $value;
		}
	}

	/**
	 * @return Direction|null
	 */
	public function getDestination()
	{
		return $this->_destination;
	}

	public function fields()
	{
		return array_merge(parent::fields(), [
			'best_offer',
			'hotel',
			'origin',
			'destination',
		]);
	}
}
