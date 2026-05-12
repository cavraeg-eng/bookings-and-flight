<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\hotels;

use Travelpayouts\modules\widgets\components\BaseWidgetsFields;

abstract class Fields extends BaseWidgetsFields
{
    public function __construct(Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }
}
