<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\httpClient;

use Travelpayouts\traits\GetterSetterTrait;
use WP_Http;

/**
 * Class Client
 * @package Travelpayouts\components\httpClient
 * @property int $timeout
 * @property int $redirection
 * @property string $httpVersion
 * @property boolean $blocking
 * @property array $headers
 * @property array $cookies
 * @property array $body
 * @property boolean $compress
 * @property boolean $decompress
 * @property boolean $sslverify
 * @property boolean $stream
 * @property string $filename
 * @property string $userAgent
 * @property string $method
 * @property array $params;
 */
class Client
{
    use GetterSetterTrait;

    protected $_timeout;
    protected $_redirection;
    protected $_httpversion;
    protected $_blocking;
    protected $_headers;
    protected $_cookies;
    protected $_body;
    protected $_compress;
    protected $_decompress;
    protected $_sslverify;
    protected $_stream;
    protected $_filename;
    protected $_userAgent;
    protected $_method = 'GET';
    protected $_client;

    public function __construct($clientOptions = [])
    {
        $this->_client = new WP_Http();
        $this->setClientOptions($clientOptions);
    }

    /**
     * @return WP_Http
     */
    protected function getClient()
    {
        return $this->_client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array|\WP_Error
     */
    protected function sendRequest($method, $url, $options = [])
    {
        $methodOptions = [
            'method' => $this->isValidMethod($method) ? strtoupper($method) : null,
        ];

        $optionsWithMethod = empty($options) || !is_array($options)
            ? $methodOptions
            : array_merge($options, $methodOptions);

        $clientOptions = $this->getClientOptions($optionsWithMethod);
        $urlWithParams = $this->prepareUrl($url, $options);
        return $this->getClient()->request($urlWithParams, $clientOptions);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return Response
     * @see WP_Http::request()
     */
    public function request($method, $url, $options = [])
    {
        $response = $this->sendRequest($method, $url, $options);
        $urlWithParams = $this->prepareUrl($url, $options);
        return new Response($response, $urlWithParams);
    }

    public function get($url, $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    public function post($url, $options = [])
    {
        return $this->request('POST', $url, $options);
    }

    public function head($url, $options = [])
    {
        return $this->request('HEAD', $url, $options);
    }

    /**
     * Добавляем к url необходимые параметры
     * @param $url
     * @param array $options
     * @return string
     */
    protected function prepareUrl($url, $options = [])
    {
        $query = $options['params'] ?? $options['query'] ?? [];
        if (!empty($query)) {
            $params = http_build_query($query);
            return strpos($url, '?') !== false
                ? $url . $params
                : "{$url}?{$params}";
        }
        return $url;
    }

    public function setClientOptions($config = [])
    {
        if (is_array($config)) {
            foreach ($config as $key => $value) {
                $attributeName = "_$key";
                $this->$attributeName = $value;
            }
        }
    }

    public function getClientOptions($options = [])
    {
        $defaultOptions = [
            'timeout' => $this->timeout,
            'redirection' => $this->redirection,
            'httpversion' => $this->httpVersion,
            'user-agent' => $this->userAgent,
            'blocking' => $this->blocking,
            'headers' => $this->headers,
            'cookies' => $this->cookies,
            'body' => $this->body,
            'compress' => $this->compress,
            'decompress' => $this->decompress,
            'sslverify' => $this->sslverify,
            'stream' => $this->stream,
            'filename' => $this->filename,
            'method' => $this->method,
        ];

        $mergedOptions = is_array($options)
            ? array_merge($defaultOptions, $options) : $defaultOptions;

        if (isset($mergedOptions['params'])) {
            unset($mergedOptions['params']);
        }

        if (isset($mergedOptions['query'])) {
            unset($mergedOptions['query']);
        }

        return array_filter($mergedOptions);
    }

    /**
     * Проверка метода на валидность
     * @param $name
     * @return bool
     */
    protected function isValidMethod($name)
    {
        $allowedMethods = ['GET', 'POST', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'];
        return in_array(strtoupper($name), $allowedMethods, true);
    }

    protected function setAttribute($name, $value, $condition)
    {
        if ($condition) {
            $this->$name = $value;
        }
    }

    public function setTimeout($value)
    {
        $this->setAttribute('_timeout', $value, is_int($value));
    }

    public function setRedirection($value)
    {
        $this->setAttribute('_redirection', $value, is_int($value));
    }

    public function setHttpVersion($value)
    {
        $this->_httpversion = $value;
    }

    public function setBlocking($value)
    {
        $this->setAttribute('_blocking', $value, is_bool($value));
    }

    public function setHeaders($value)
    {
        $this->setAttribute('_headers', $value, is_array($value));
    }

    public function setCookies($value)
    {
        $this->setAttribute('_cookies', $value, is_array($value));
    }

    public function setBody($value)
    {
        $this->setAttribute('_body', $value, is_array($value));
    }

    public function setCompress($value)
    {
        $this->setAttribute('_compress', $value, is_bool($value));
    }

    public function setDecompress($value)
    {
        $this->setAttribute('_decompress', $value, is_bool($value));
    }

    public function setSslverify($value)
    {
        $this->setAttribute('_sslverify', $value, is_bool($value));
    }

    public function setStream($value)
    {
        $this->setAttribute('_stream', $value, is_bool($value));
    }

    public function setFilename($value)
    {
        $this->_filename = $value;
    }

    public function setUserAgent($value)
    {
        $this->setAttribute('_userAgent', $value, is_string($value));
    }

    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function getRedirection()
    {
        return $this->_redirection;
    }

    public function getHttpVersion()
    {
        return $this->_httpversion;
    }

    public function getBlocking()
    {
        return $this->_blocking;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function getCookies()
    {
        return $this->_cookies;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function getCompress()
    {
        return $this->_compress;
    }

    public function getDecompress()
    {
        return $this->_decompress;
    }

    public function getSslverify()
    {
        return $this->_sslverify;
    }

    public function getStream()
    {
        return $this->_stream;
    }

    public function getFilename()
    {
        return $this->_filename;
    }

    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    public function setMethod($value)
    {
        $this->setAttribute('_method', $value, $this->isValidMethod($value));
    }

    public function getMethod()
    {
        return $this->_method;
    }
}
