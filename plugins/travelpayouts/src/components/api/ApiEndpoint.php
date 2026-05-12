<?php

namespace Travelpayouts\components\api;

use Travelpayouts;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\traits\SingletonTrait;

/**
 * Class ApiEndpoint
 * @package Travelpayouts\components\api
 *
 */
abstract class ApiEndpoint extends BaseObject
{
    use SingletonTrait;
    protected $_cacheTime = 3600;

    /**
     * @return CachedClient
     */
    protected function getClient(): CachedClient
    {
        return new CachedClient($this->clientOptions(), $this->_cacheTime);
    }

    /**
     * Возвращает массив опций для http клиента
     * @return array
     */
    protected function clientOptions(): array
    {
        return [];
    }

    public function getResponse(): ?Travelpayouts\components\httpClient\CachedResponse
    {
        return null;
    }
}
