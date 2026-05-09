<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\httpClient;
use Travelpayouts\Vendor\Adbar\Dot;
use Requests_Utility_CaseInsensitiveDictionary;
use Travelpayouts\traits\GetterSetterTrait;
use WP_Error;
use WP_HTTP_Cookie;
use WP_HTTP_Requests_Response;

/**
 * Class Response
 * @package Travelpayouts\components\httpClient
 * @property-read null|WP_HTTP_Requests_Response $httpResponse
 * @property-read null|Dot $response
 * @property-read bool $isError
 * @property-read int|null $statusCode
 * @property-read WP_Error|null $errorObject
 * @property-read mixed|null $raw
 * @property-read null|Requests_Utility_CaseInsensitiveDictionary $headers
 * @property-read  WP_HTTP_Cookie[]|null $cookies
 * @property-read  mixed|null $body
 * @property-read mixed|null $json
 * @property-read string $requestUrl
 */
class Response
{
    use GetterSetterTrait;

    protected $_response;
    protected $_error;
    protected $_httpResponse;
    protected $_json;
    protected $_requestUrl;

    /**
     * Response constructor.
     * @param array|WP_Error $response
     * @param string $url
     */
    public function __construct($response, $requestUrl = '')
    {
        if ($response instanceof WP_Error) {
            $this->_error = $response;
        } elseif (is_array($response)) {
            $this->_response = new Dot($response);
        }
        $this->_requestUrl = $requestUrl;
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->_requestUrl;
    }

    /**
     * @return Dot
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return null|WP_HTTP_Requests_Response
     */
    public function getHttpResponse()
    {
        if (!$this->_httpResponse && $this->response) {
            $response = $this->response->get('http_response', null);
            if ($response instanceof WP_HTTP_Requests_Response) {
                $this->_httpResponse = $response;
            }
        }
        return $this->_httpResponse;
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->httpResponse
            ? $this->httpResponse->get_status()
            : null;
    }

    /**
     * @return bool
     */
    public function getIsError()
    {
        return !empty($this->_error);
    }

    public function getIsSuccess()
    {
        $statusCode = $this->getStatusCode();
        return $statusCode && $statusCode >= 200 && $statusCode < 300 && !$this->getIsError();
    }

    /**
     * @return WP_Error|null
     */
    public function getErrorObject()
    {
        return $this->_error ? $this->_error : null;
    }


    /**
     * @return mixed|null
     */
    public function getRaw()
    {
        return $this->response ?
            $this->response->get('raw')
            : null;
    }

    /**
     * @return null|Requests_Utility_CaseInsensitiveDictionary
     */
    public function getHeaders()
    {
        return $this->httpResponse
            ? $this->httpResponse->get_headers()
            : null;
    }

    /**
     * @return WP_HTTP_Cookie[]|null
     */
    public function getCookies()
    {
        return $this->httpResponse
            ? $this->httpResponse->get_cookies()
            : null;
    }

    /**
     * @return mixed|null
     */
    public function getBody()
    {
        return $this->httpResponse
            ? $this->httpResponse->get_data()
            : null;
    }

    /**
     * @return array|null
     */
    public function getJSON()
    {
        if (!$this->_json) {
            $body = $this->getBody();
            if ($body) {
                $jsonData = json_decode($body, true);
                if ($jsonData !== null && json_last_error() === JSON_ERROR_NONE) {
                    $this->_json = $jsonData;
                }
            }
        }
        return $this->_json;
    }
}
