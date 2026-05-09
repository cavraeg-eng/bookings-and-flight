<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\api;
use Travelpayouts\Vendor\apimatic\jsonmapper\JsonMapper;
use ArrayAccess;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\interfaces\Arrayable;
use Travelpayouts\traits\ArrayableTrait;
use Travelpayouts\traits\SingletonTrait;

abstract class ApiResponseObject extends BaseInjectedObject implements Arrayable, ArrayAccess
{
    use SingletonTrait;
    use ArrayableTrait;

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        return $this->$offset;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->$offset = null;
    }

    /**
     * @param array $response
     * @return static
     */
    public static function createFromArray(array $response)
    {
        $mapper = new JsonMapper();
        $mapper->bEnforceMapType = false;
        $mapper->bExceptionOnMissingData = false;
        /** @var self $mappedResponse */
        $mappedResponse = $mapper->map(json_decode(json_encode($response)), new static);
        return $mappedResponse;
    }
}
