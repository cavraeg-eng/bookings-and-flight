<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\traits\SingletonTrait;

class Request
{
    use SingletonTrait;

    /**
     * @var array
     */
    protected $_queryParams;
    /**
     * @var array
     */
    protected $_postParams;
    /**
     * @var array
     */
    protected $_inputParams;

    /**
     * @return array|mixed|object|null
     */
    public function getInputData()
    {
        if (!$this->_inputParams) {
            try {
                $jsonData = file_get_contents('php://input');
                $this->_inputParams = json_decode($jsonData, true);
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->_inputParams;
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getInputParam($name, $defaultValue = null)
    {
        $data = $this->getInputData();
        if ($data) {
            return $data[$name] ?? $defaultValue;
        }
        return $defaultValue;
    }

    /**
     * Returns the request parameters given in the [[queryString]].
     * This method will return the contents of `$_GET` if params where not explicitly set.
     * @return array the request GET parameter values.
     * @see setQueryParams()
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            return $_GET;
        }

        return $this->_queryParams;
    }

    /**
     * Sets the request [[queryString]] parameters.
     * @param array $values the request query parameters (name-value pairs)
     * @see getQueryParam()
     * @see getQueryParams()
     */
    public function setQueryParams($values)
    {
        $this->_queryParams = $values;
    }

    /**
     * Returns GET parameter with a given name. If name isn't specified, returns an array of all GET parameters.
     * @param string|null $name the parameter name
     * @param mixed $defaultValue the default parameter value if the parameter does not exist.
     * @return array|mixed
     */
    public function get(string $name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        }

        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter passed to this method will be returned.
     * @param string $name the GET parameter name.
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getBodyParam()
     */
    public function getQueryParam(string $name, $defaultValue = null)
    {
        $params = $this->getQueryParams();
        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod(): string
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }

    /**
     * @return array
     */
    public function getPostParams()
    {
        if (!$this->_postParams && $this->getMethod() === 'POST') {
            $this->_postParams = $_POST;
        }

        return $this->_postParams;
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed|null
     */
    public function getPostParam(string $name, $defaultValue = null)
    {
        $data = $this->getPostParams();
        if ($data) {
            return $data[$name] ?? $defaultValue;
        }
        return $defaultValue;
    }
}
