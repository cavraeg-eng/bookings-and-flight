<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\httpClient;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\Container;

/**
 * Class CachedClient
 * @method CachedResponse get($url, $options = [])
 * @method CachedResponse post($url, $options = [])
 * @method CachedResponse head($url, $options = [])
 */
class CachedClient extends Client
{
    /**
     * @Inject
     * @var Cache
     */
    protected $cache;
    /**
     * @var int
     */
    protected $cacheTime;

    public function __construct($clientOptions = [], $cacheTime = 60 * 60 * 24)
    {
        Container::getInstance()->inject($this);
        $this->cacheTime = $cacheTime;
        parent::__construct($clientOptions);
    }

    protected function sendRequest($method, $url, $options = [])
    {
        $cacheKey = $this->getCacheKey($method, $url, $options);
        /** @var false|array $response */
        $response = $this->cache->get($cacheKey);
        if ($response === false) {
            $response = parent::sendRequest($method, $url, $options);
            if (is_array($response)) {
                $this->cache->set($cacheKey, $response, $this->cacheTime);
            }
        }
        return $response;
    }

    protected function getCacheKey($method, $url, $options = []): string
    {
        return md5(json_encode([$method, $url, $this->getClientOptions($options)]));
    }

    /**
     * @param int $cacheTime
     */
    public function setCacheTime(int $cacheTime): void
    {
        $this->cacheTime = $cacheTime;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return CachedResponse
     */
    public function request($method, $url, $options = []): CachedResponse
    {
        $response = $this->sendRequest($method, $url, $options);
        $urlWithParams = $this->prepareUrl($url, $options);
        $cacheKey = $this->getCacheKey($method, $url, $options);
        return new CachedResponse($response, $urlWithParams, $this->cache, $cacheKey);
    }
}
