<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\base\cache;

/**
 * Class TransientCache
 * @package Travelpayouts\components\base\cache
 *
 */
class TransientCache extends Cache
{
    /**
     * @inheritdoc
     */
    public $keyPrefix = TRAVELPAYOUTS_PLUGIN_NAME.'_';
    
    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        return get_transient($key);
    }

    /**
     * @inheritDoc
     */
    protected function setValue($key, $value, $duration)
    {
        return set_transient($key, $value, $duration);
    }

    /**
     * @inheritDoc
     */
    protected function addValue($key, $value, $duration)
    {
        return $this->getValue($key) === false
            ?
            $this->setValue($key, $value, $duration)
            : false;
    }

    /**
     * @inheritDoc
     */
    protected function deleteValue($key)
    {
        return delete_transient($key);
    }

    /**
     * @inheritDoc
     */
    protected function flushValues()
    {
        delete_expired_transients();
        return true;
    }
}
