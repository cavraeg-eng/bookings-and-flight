<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models\hotellook;

use Travelpayouts\components\Model;

/**
 * Class HotellookAutocompleteHotel
 * @package Travelpayouts\modules\searchForms\models\hotellook
 * @property-read string $label
 */
class HotellookAutocompleteHotel extends Model
{
    /**
     * @var null
     */
    public $state;
    /**
     * @var int
     */
    public $stars;
    /**
     * @var string
     */
    public $locationFullName;
    /**
     * @var string
     */
    public $latinLocationFullName;
    /**
     * @var string
     */
    public $hotelFullName;
    /**
     * @var array{lat:number,lon: number}
     */
    public $location;
    /**
     * @var string
     */
    public $timezone;
    /**
     * @var number
     */
    public $timezonesec;
    /**
     * @var number
     */
    public $id;
    /**
     * @var number
     */
    public $locationId;
    /**
     * @var number
     */
    public $photoCount;
    /**
     * @var string
     */
    public $city;
    /**
     * @var string
     */
    public $latinCity;
    /**
     * @var string
     */
    public $clar;
    /**
     * @var string
     */
    public $latinClar;
    /**
     * @var string
     */
    public $latinCountry;
    /**
     * @var null
     */
    public $locationHotelsCount;
    /**
     * @var number
     */
    public $rating;
    /**
     * @var string
     */
    public $country;
    /**
     * @var number
     */
    public $distance;
    /**
     * @var number
     */
    public $_score;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $latinName;
    /**
     * @var string
     */
    public $address;
    /**
     * @var number
     */
    public $photos;
    /**
     * @var number
     */
    public $countryId;
    /**
     * @var boolean
     */
    public $isOutOfService;

    public function extraFields()
    {
        return ['searchFormShortcodeValue'];
    }

    /**
     * @return string
     */
    public function getSearchFormShortcodeValue()
    {
        return implode(', ', [$this->name, $this->locationFullName, $this->id, 'hotel', $this->country]);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return implode(', ', [$this->name, $this->locationFullName]);
    }
}
