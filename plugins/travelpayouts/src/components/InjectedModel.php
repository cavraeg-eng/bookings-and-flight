<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

class InjectedModel extends Model
{
    public function __construct($config = [])
    {
        Container::getInstance()->inject($this);
        parent::__construct($config);
    }
}
