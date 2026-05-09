<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\httpClient;

use Travelpayouts\components\base\cache\CacheInterface;

class CachedResponse extends Response
{
    /**
     * @var CacheInterface|null
     */
    protected $_cache;
    /**
     * @var string
     */
    protected $_cacheKey = '';

    public function __construct($response, string $requestUrl = '', CacheInterface $cacheInstance = null, string $cacheKey = '')
    {
        $this->_cacheKey = $cacheKey;
        $this->_cache = $cacheInstance;
        parent::__construct($response, $requestUrl);
    }

    /**
     * @return CacheInterface|null
     */
    public function getCache(): ?CacheInterface
    {
        return $this->_cache;
    }

    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->_cacheKey;
    }

    public function deleteCache()
    {
        if (($cache = $this->getCache()) && ($cacheKey = $this->getCacheKey())) {
           return $cache->delete($cacheKey);
        }
        return false;
    }
}