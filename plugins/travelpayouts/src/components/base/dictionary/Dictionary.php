<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\base\dictionary;
use Travelpayouts\Vendor\Adbar\Dot;
use Exception;
use Travelpayouts;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\httpClient\Client;
use Travelpayouts\traits\GetterSetterTrait;

/**
 * Class Dictionary
 * @package Travelpayouts\src\components\dictionary
 * @property string $lang
 * @property-read array $data
 * @property-read string $primaryKey
 * @property-read Cache $cache
 * @property-read string $className
 */
abstract class Dictionary extends BaseObject
{
    use GetterSetterTrait;
    const CACHE_TIME = 604800; // one week

    public $_pk = 'code';
    protected $type;
    protected $_lang;
    protected $_data = false;
    protected $_httpClient;
    protected $_localesFallback = [];
    protected $_locales = [];

    protected $itemClass;

    private static $_instances = [];


    public function init()
    {
        if (!$this->itemClass) throw new Exception("[{$this->className}]: item_class property must be set");
    }

    /**
     * @param array $config
     * @return Dictionary
     */
    public static function getInstance($config = [])
    {
        $classNameWithArgs = [
            static::class,
            json_encode($config)
        ];

        $className = md5(implode('_', array_merge($classNameWithArgs)));
        if (!isset(self::$_instances[$className])) {
            $instanceName = static::class;
            self::$_instances[$className] = new $instanceName($config);
        }

        return self::$_instances[$className];
    }

    public function setLang($lang)
    {
        if (!is_string($lang)) throw new Exception('Language param must be a string');
        if (in_array($lang, $this->_locales, true)) {
            $this->_lang = $lang;
        } elseif (array_key_exists($lang, $this->_localesFallback)) {
            $this->_lang = $this->_localesFallback[$lang];
        } else {
            $this->_lang = 'en';
        }
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->_lang;
    }

    /**
     * Get api data
     * @param $type
     * @param $lang
     * @return array
     * @throws Exception
     */
    abstract protected function fetchData();

    /**
     * @return Client
     */
    protected function getHttpClient()
    {
        if (!$this->_httpClient) {
            $this->_httpClient = new Client([
                'timeout' => 15,
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * Get response
     * @return array
     * @throws Exception
     */
    public function getData()
    {
        if (!$this->_data) {
            $response = $this->fetchData();
            $mappedResponse = $this->mapResponse($response);
            $this->_data = new Dot($mappedResponse);
        }
        return $this->_data->all();
    }

    /**
     * @param $response
     * @return array
     */
    public function mapResponse($response)
    {
        if (is_array($response)) {
            $mappedResponse = [];
            foreach ($response as $value) {
                if (isset($value[$this->primaryKey])) {
                    $item_id = $value[$this->primaryKey];
                    $mappedResponse[$item_id] = $value;
                }
            }
            return $mappedResponse;
        }
        return [];
    }

    /**
     * Return Item instance
     * @param $code
     * @return Item|EmptyItem
     */
    public function getItem($code)
    {
        try {
            // load api response data
            if (!$this->_data) $this->getData();
            $data = $this->_data->get($code);
            if ($data && class_exists($this->itemClass)) {
                $itemInstance = $this->itemClass;
                return new $itemInstance(array_merge(
                    $data,
                    [
                        '_data' => $data,
                        '_lang' => $this->lang,
                    ]
                ));
            }
            return new EmptyItem();
        } catch (Exception $e) {
            return new EmptyItem();
        }
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return static::class;
    }

    /**
     * Парсим json
     * @param $file
     * @param array $defaultValue
     * @return array|mixed|object|null
     */
    protected function parseJson($file, $defaultValue = [])
    {
        $data = json_decode($file, true);
        return $data === null && json_last_error() !== JSON_ERROR_NONE
            ? $defaultValue
            : $data;
    }

    /**
     * @param $method
     * @param $url
     * @param array $options
     * @return Travelpayouts\components\httpClient\Response|null
     */
    protected function sendRequest($method, $url, $options = [])
    {
        $client = $this->getHttpClient();
        if ($client) {
            $response = $client->request($method, $url, $options);
            return !$response->isError && $response->statusCode === 200
                ? $response
                : null;
        }
        return null;
    }

    /**
     * @return string
     */
    protected function getPrimaryKey()
    {
        return $this->_pk;
    }

    /**
     * @return Cache
     */
    protected function getCache()
    {
        return Travelpayouts::getInstance()->cache;
    }
}
