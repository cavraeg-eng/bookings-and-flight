<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\models;

use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\httpClient\Client;
use Travelpayouts\components\Model;
use Travelpayouts\helpers\ArrayHelper;

/**
 * @property-read string $fromCity
 * @property-read string $toCity
 * @property-read string $hotelCity
 * @property-read string $dateAdd
 */
class SearchFormMigrationItem extends Model
{
    const CITY_HOTEL_REGEXP = '/(?<id>\d+),?\s(?<type>city|hotel),/';
    const CITY_REGEXP = '/\[(?<id>\w{3})\]/';

    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    protected $_code_form;
    /**
     * @var string
     */
    protected $_date_add;

    /**
     * @var array|string
     */
    protected $_fromCity;
    /**
     * @var array|string
     */
    protected $_toCity;
    /**
     * @var array|string
     */
    protected $_cityHotel;
    /**
     * @var Client
     */
    protected $_client;
    /**
     * @var string
     */
    protected $_locale = 'ru';
    /**
     * @var string
     */
    protected $_cityHotelType;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;

    }

    /**
     * @param string $value
     * @return self
     */
    public function setFrom_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_REGEXP, $value, $matches)) {
            $this->_fromCity = $matches['id'];
        }
        return $this;

    }

    /**
     * @param string $value
     * @return self
     */
    public function setTo_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_REGEXP, $value, $matches)) {
            $this->_toCity = $matches['id'];
        }
        return $this;
    }

    /**
     * @param $value
     * @return self
     */
    public function setCode_form($value)
    {
        if (is_string($value)) {
            $this->_code_form = str_replace('\\', '', $value);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode_form()
    {
        return $this->_code_form;
    }

    /**
     * @return array|string
     */
    public function getFromCity()
    {
        if (is_string($this->_fromCity)) {
            $data = $this->getCityDataById($this->_fromCity);
            if ($data) {
                $this->_fromCity = $data;
            }
        }

        return $this->_fromCity;
    }

    /**
     * @return array|string
     */
    public function getToCity()
    {
        if (is_string($this->_toCity)) {
            $data = $this->getCityDataById($this->_toCity);
            if ($data) {
                $this->_toCity = $data;
            }
        }

        return $this->_toCity;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setHotel_city($value)
    {
        if (is_string($value) && preg_match(self::CITY_HOTEL_REGEXP, $value, $matches)) {
            $this->_cityHotel = $matches['id'];
            if (in_array($matches['type'], ['hotel', 'city'])) {
                $this->_cityHotelType = $matches['type'];
            }
        }
        return $this;
    }

    /**
     * @return array|string
     */
    public function getHotelCity()
    {
        if (is_string($this->_cityHotel) && $this->_cityHotelType) {
            $data = $this->getHotelDataById($this->_cityHotel, $this->_cityHotelType);
            if ($data) {
                $this->_cityHotel = $data;
            }
        }

        return $this->_cityHotel;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setDate_add($value)
    {
        if (is_string($value)) {
            $date = date(SearchFormModel::DATE_FORMAT, $value);
            if ($date) {
                $this->_date_add = $date;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDateAdd()
    {
        return $this->_date_add;
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'from_city' => [$this, 'getFromCity'],
            'to_city' => [$this, 'getToCity'],
            'hotel_city' => [$this, 'getHotelCity'],
            'date_add' => [$this, 'getDateAdd'],
            'code_form',
        ]);
    }

    /**
     * Получаем информацию о городе из эндпоинта
     * @param $id
     * @return mixed|null
     */
    protected function getCityDataById($id)
    {
        $client = $this->getClient();
        $response = $client->get('https://autocomplete.travelpayouts.com/places2', [
            'query' => [
                'locale' => $this->getLocale(),
                'term' => $id,
                'types[]' => 'city',
            ],
        ]);
        if (!$response->isError) {
            $data = $response->getJSON();
            if (count($data)) {
                return ArrayHelper::getFirst($data);
            }
        }
        return null;
    }

    protected function getHotelDataById($id, $type)
    {
        $client = $this->getClient();
        $response = $client->get('https://yasen.hotellook.com/autocomplete', [
            'query' => [
                'lang' => $this->getLocale(),
                'term' => $id,
            ],
        ]);
        $searchIndex = $type === 'hotel' ? 'hotels' : 'cities';
        if (!$response->isError) {
            $data = $response->getJSON();
            if (count($data) && isset($data[$searchIndex]) && count($data[$searchIndex])) {
                $firstElement = ArrayHelper::getFirst($data[$searchIndex]);

                if ($type === 'hotel') {
                    return [
                        'name' => $firstElement['name'],
                        'location' => $firstElement['locationFullName'],
                        'hotels_count' => '',
                        'search_id' => $firstElement['id'],
                        'search_type' => 'hotel',
                        'country_name' => $firstElement['country'],
                    ];
                }

                if ($type === 'city') {
                    return [
                        'name' => $firstElement['city'],
                        'location' => $firstElement['fullname'],
                        'hotels_count' => $firstElement['hotelsCount'],
                        'search_id' => $firstElement['id'],
                        'search_type' => 'city',
                        'country_name' => $firstElement['country'],
                    ];
                }
            }
        }
        return null;
    }

    /**
     * @return SearchFormModel|null
     */
    public function getSearchFormModel()
    {
        $searchForm = new SearchFormModel($this->toArray());
        try {
            return $searchForm->validate() ? $searchForm->setAllowSaveWithId(true) : null;
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->_client) {
            $this->_client = new CachedClient([
                'timeout' => 15,
                'headers' => [
                    'Accept-Encoding' => 'gzip, deflate',
                    'Accept-Language' => '*',
                ],
            ]);
        }
        return $this->_client;
    }

    /**
     * @param $data
     * @return self[]
     */
    public static function createFromCollection($data)
    {
        $result = [];
        if (is_array($data) && ArrayHelper::isIndexed($data)) {
            self::sortCollectionById($data);
            foreach ($data as $item) {
                $result[] = new self($item);
            }
        }

        return $result;
    }

    protected static function sortCollectionById(&$data)
    {
        usort($data, static function ($a, $b) {
            $idA = isset($a['id']) ? $a['id'] : null;
            $idB = isset($b['id']) ? $b['id'] : null;
            if ($idA == $idB) {
                return 0;
            }
            return ($idA < $idB) ? -1 : 1;
        });
    }

}
