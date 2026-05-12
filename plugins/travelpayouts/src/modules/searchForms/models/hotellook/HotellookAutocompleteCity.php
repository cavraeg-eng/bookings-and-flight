<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models\hotellook;

use Travelpayouts\components\Model;

/**
 * Class HotellookAutocompleteCity
 * @package Travelpayouts\modules\searchForms\models\hotellook
 * @property string $name
 * @property string $label
 */
class HotellookAutocompleteCity extends Model
{
    /**
     * @var string
     */
    public $countryCode;
    /**
     * @var string
     */
    public $country;
    /**
     * @var string
     */
    public $latinFullName;
    /**
     * @var string
     */
    public $fullname;
    /**
     * @var string
     */
    public $clar;
    /**
     * @var string
     */
    public $latinClar;
    /**
     * @var array{lat:number,lon: number}
     */
    public $location;
    /**
     * @var number
     */
    public $hotelsCount;
    /**
     * @var array
     */
    public $iata;
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
    public $timezone;
    /**
     * @var number
     */
    public $timezonesec;
    /**
     * @var string
     */
    public $latinCountry;
    /**
     * @var number
     */
    public $id;
    /**
     * @var number
     */
    public $countryId;
    /**
     * @var number
     */
    public $_score;
    /**
     * @var boolean
     */
    public $isOutOfService;
    /**
     * @var null
     */
    public $state;

    public function extraFields()
    {
        return ['searchFormShortcodeValue'];
    }

    /**
     * @return string
     */
    public function getSearchFormShortcodeValue()
    {
        return implode(', ', [$this->city, $this->country, $this->hotelsCount, $this->id, 'city', $this->country]);
    }

    public function getName()
    {
        return $this->city;
    }

    public function getLabel()
    {
        return implode(', ', [$this->city, $this->country]);
    }

}
