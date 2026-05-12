<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\citySuggest;

use Travelpayouts;
use Travelpayouts\components\dictionary\Cities;
use Travelpayouts\components\Model;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class CitySuggestItem
 * @package Travelpayouts\modules\tables\components\api\travelpayouts\citySuggest
 * @property-read float|null $latitude
 * @property-read float|null $longitude
 * @property-read string|null $mainIataCode
 */
class CitySuggestItem extends Model
{
    public $cityName;
    public $countryName;
    public $hotelsCount;
    public $iata;
    public $id;
    public $location;
    protected $locale;
    /**
     * @var Cities
     */
    protected $_dictionary;


    public function rules()
    {
        return [
            [
                [
                    'cityName',
                    'countryName',
                    'locale',
                ],
                'string',
            ],
            [
                [
                    'id',
                    'hotelsCount',
                ],
                'number',
            ],
            [
                ['location'],
                'arrayValidator',
                'params' => [
                    'keys' => [
                        'lat',
                        'lon',
                    ],
                ],
            ],
            [
                [
                    'iata',

                ],
                'indexedArrayValidator',
            ],
        ];
    }

    public function arrayValidator($attribute, $params)
    {
        if (isset($params['keys']) && is_array($params['keys'])) {
            $keys = $params['keys'];
            $value = $this->$attribute;

            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    $this->add_error($attribute, Travelpayouts::__('Parameter "{:key}" not found in "{:attribute}"', [
                        ':key' => $key,
                        ':attribute' => $attribute,
                    ]));
                }
            }
        }
    }

    public function indexedArrayValidator($attribute)
    {
        if (!ArrayHelper::isIndexed($this->$attribute)) {
            $this->add_error($attribute, Travelpayouts::__('Attribute "{:attribute}" must be indexed array', [':attribute' => $attribute]));
        }
//        latitude longitude
    }

    /**
     * @return float|null
     */
    public function getLatitude()
    {
        return isset($this->location['lat'])
            ? $this->location['lat']
            : null;
    }

    /**
     * @return float|null
     */
    public function getLongitude()
    {
        return isset($this->location['lon'])
            ? $this->location['lon']
            : null;
    }

    public function getMainIataCode()
    {
        return is_array($this->iata) && isset($this->iata[0])
            ? $this->iata[0]
            : null;
    }

    public function getCaseName($case)
    {
        return $this->getDictionary()->getItem($this->mainIataCode)->getName($case);
    }

    /**
     * @return Cities
     */
    public function getDictionary()
    {
        if (!$this->_dictionary) {
            $this->_dictionary = Cities::getInstance(['lang' => $this->locale]);
        }
        return $this->_dictionary;
    }
}
