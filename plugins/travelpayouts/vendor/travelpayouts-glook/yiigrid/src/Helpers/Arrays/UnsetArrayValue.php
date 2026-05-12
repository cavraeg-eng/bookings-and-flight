<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Arrays;

class UnsetArrayValue
{
    /**
     * Restores class state after using `var_export()`.
     *
     * @param array $state
     * @return self
     * @see var_export()
     */
    public static function __set_state($state)
    {
        return new self();
    }
}
