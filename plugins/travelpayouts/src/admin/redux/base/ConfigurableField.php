<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;

use Travelpayouts\components\BaseObject;

abstract class ConfigurableField extends \Redux_Travelpayouts_Field
{
    /**
     * @inheritDoc
     */
    public function __construct($field = [], $value = null, $parent = null)
    {
        parent::__construct($field, $value, $parent);
        BaseObject::configure($this, $field);
        $this->init();
    }

    public function init(): void
    {

    }
}