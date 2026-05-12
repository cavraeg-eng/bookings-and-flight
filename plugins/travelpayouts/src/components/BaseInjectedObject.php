<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

abstract class BaseInjectedObject extends BaseObject
{
	public function __construct($config = [])
	{
        self::inject($this);
		parent::__construct($config);
	}

    public static function inject($object)
    {
        Container::getInstance()->inject($object);
    }
}
