<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\apimatic\jsonmapper\JsonMapper;
use Travelpayouts\Vendor\apimatic\jsonmapper\JsonMapperException;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts;
use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\httpClient\Client;
use Travelpayouts\components\notices\Notice;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Class ApiModel
 * @package Travelpayouts\src\components
 * @property-read array $api_data
 * @property array|null $response
 * @property-read array $debugData
 */
abstract class ApiModel extends InjectedModel
{
    /**
     * @Inject
     * @var Notices
     */
    protected $notices;

    /**
     * @Inject
     * @var Travelpayouts\modules\settings\SettingsForm
     */
    protected $settingsSection;

    /**
     * @var BaseObject
     */
    protected $responseClass;

    /**
     * Опции для httpClient\Client
     * @see getHttpClient()
     * @var array
     */
    protected $clientOptions = [
        'timeout' => 15,
        'headers' => [
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => '*',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
        ],
    ];
    /**
     * @var array|null
     */
    protected $_response;
    /**
     * @var string[]
     */
    private $_requestList = [];

    protected $cacheTime = 60 * 5;

    protected $_responseModels;

    /**
     * @return Client
     */
    protected function getHttpClient()
    {
        return new CachedClient($this->clientOptions, $this->cacheTime);
    }

    /**
     * @return string
     */
    protected function getRequestQueryString()
    {
        return '?' . http_build_query($this->toArray());
    }

    /**
     * @return string|null
     * @see getRequestQueryString()
     */
    public function getRequestUrl()
    {
        return $this->endpointUrl()
            ? $this->endpointUrl() . $this->getRequestQueryString()
            : null;
    }

    /**
     * @param array|null $data
     */
    protected function setResponse($data)
    {
        $this->_response = $data;
    }

    /**
     * @return array|null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Функция, которая будет отдавать данные от апи
     * @return array|mixed
     */
    abstract protected function request();

    /**
     * Возвращаем данные из апи попутно вызывая коллбеки
     * @return array|bool
     */
    final public function sendRequest()
    {
        $this->response = null;
        try {
            if ($this->validate()) {
                $this->response = $this->request();
                $this->afterRequest();
                $this->notifyErrors();
                return $this->response;
            }

            $this->notifyErrors();
        } catch (Exception $e) {
            Travelpayouts::getInstance()->logger->error($e->getMessage(), [
                $this->attributes,
            ]);
        }
        return [];
    }

    /**
     * Коллбек, вызываемый после назначения $this->response
     * На этом этапе необходимо применять мутации для обогащения данных
     * @return void
     */
    protected function afterRequest()
    {
    }

    /**
     * Собираем корректный url с аттрибутами и отправляем запрос
     * @return array|bool
     */
    protected function fetchApi()
    {
        return $this->fetchRemoteContent($this->getRequestUrl());
    }

    /**
     * @param string $url
     * @return bool|false|mixed|null
     * @see getRequestUrl()
     */
    private function fetchRemoteContent(string $url)
    {
        $this->addRequestUrl($url);
        $this->clientOptions = ArrayHelper::mergeRecursive($this->clientOptions, [
            'headers' => [
                'Host' => parse_url($url, PHP_URL_HOST),
            ],
        ]);

        return $this->getHttpClient()->get($url)->json;
    }

    /**
     * @param string|array $urlList
     */
    protected function addRequestUrl($urlList)
    {
        $value = !is_array($urlList)
            ? [$urlList]
            : $urlList;
        $this->_requestList = array_merge($this->_requestList, $value);
    }

    public function getDebugData()
    {
        return $this->_requestList;
    }

    /**
     * @return string
     */
    abstract protected function endpointUrl();
    /**
     * Добавляет ошибки в notices которые отображаются в админке
     */
    protected function notifyErrors()
    {
        if (!$this->settingsSection->getIsTableNoticesDisabled()) {
            foreach ($this->getErrors() as $key => $error) {
                $noticeName = implode('-', [
                    TRAVELPAYOUTS_PLUGIN_NAME,
                    'validationNotice',
                    $key,
                ]);

                $this->notices->add(
                    Notice::create($noticeName)
                        ->setType(Notice::NOTICE_TYPE_ERROR)
                        ->setTitle(Travelpayouts::__('Validation failed'))
                        ->setDescription(implode(' ', $error))
                );
            }
        }
    }

    /**
     * @return BaseObject[]
     * @throws InvalidConfigException
     */
    public function getResponseModels(): array
    {
        if (!$this->_responseModels && !$this->_response && $this->responseClass) {
            $response = $this->sendRequest();
            $result = [];
            foreach ($response as $item) {
                $result[] = BaseObject::createObject(array_merge($item, [
                    'class' => $this->responseClass,
                ]));
            }
            $this->_responseModels = $result;
        }

        return $this->_responseModels;
    }

    protected $mappedResponses = [];

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     * @throws JsonMapperException
     */
    public function getMappedResponse(string $class)
    {
        $mappedResponse = $this->mappedResponses[$class] ?? null;
        if (!$mappedResponse) {
            $response = $this->getResponse() ?? $this->sendRequest();
            $mapper = new JsonMapper();
            $mapper->bEnforceMapType = false;
            $mapper->bExceptionOnMissingData = false;
            if (!is_array($response)) {
                $objectResponse = (object)[];
            } else {
                $objectResponse = ArrayHelper::toObject($response);
            }
            $mappedResponse = $mapper->map($objectResponse, new $class);
            $this->mappedResponses[$class] = $mappedResponse;
        }
        return $mappedResponse;
    }

    /**
     * @param class-string $responseClass
     */
    public function setResponseClass(string $responseClass): void
    {
        $this->responseClass = $responseClass;
    }
}
