<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Exception;
use Travelpayouts\components\BaseObject;
use Travelpayouts\components\exceptions\HeadersAlreadySentException;
use Travelpayouts\components\exceptions\HttpException;
use Travelpayouts\components\exceptions\InvalidArgumentException;
use Travelpayouts\traits\SingletonTrait;

/**
 * @property int $statusCode
 * @property-read bool $isSuccessful
 */
class Response extends BaseObject
{
    use SingletonTrait;

    public $statusText = 'OK';
    /**
     * @var mixed
     */
    public $content;
    /**
     * @var bool
     */
    protected $isSent = false;
    /**
     * @var mixed|null
     */
    protected $_statusCode = 200;
    /**
     * @var array
     */
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
    /**
     * @var string
     */
    private $version;
    /**
     * @var Exception|null
     */
    protected $exception;

    public function init()
    {
        if ($this->version === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->version = '1.0';
            } else {
                $this->version = '1.1';
            }
        }
    }

    /**
     * @param Exception $e
     * @return void
     * @throws InvalidArgumentException
     */
    public function setStatusCodeByException(Exception $e)
    {
        $this->exception = $e;
        if ($e instanceof HttpException) {
            $this->setStatusCode($e->statusCode);
        } else {
            $this->setStatusCode(500);
        }
        $this->content = $this->exceptionContent();
    }

    /**
     * @return mixed
     */
    public function send()
    {
        if ($this->isSent) {
            return;
        }
        $this->setHeaders();
        $this->beforeSend();
        $this->sendContent();
        $this->isSent = true;
    }

    /**
     * @return void
     */
    protected function sendContent()
    {
        echo $this->content;
        die();
    }

    /**
     * @return bool whether this response has a valid [[statusCode]].
     */
    public function getIsInvalid()
    {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    /**
     * @return bool whether this response is successful
     */
    public function getIsSuccessful()
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * @return int the HTTP status code to send with the response.
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * @throws HeadersAlreadySentException
     */
    protected function setHeaders()
    {
        if (headers_sent($file, $line)) {
            throw new HeadersAlreadySentException($file, $line);
        }

        $statusCode = $this->getStatusCode();
        header("HTTP/{$this->version} {$statusCode} {$this->statusText}");
    }

    /**
     * @param $value
     * @param $text
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setStatusCode($value, $text = null)
    {
        if ($value === null) {
            $value = 200;
        }
        $this->_statusCode = (int)$value;
        if ($this->getIsInvalid()) {
            throw new InvalidArgumentException("The HTTP status code is invalid: $value");
        }
        if ($text === null) {
            $this->statusText = isset(static::$httpStatuses[$this->_statusCode]) ? static::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }

        return $this;
    }

    /**
     * @return void
     */
    public function beforeSend()
    {
    }

    /**
     * @return mixed
     */
    public function exceptionContent()
    {
        return $this->getStatusCode() . "\n" . $this->statusText;
    }
}
