<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary\items;

use Travelpayouts\components\base\dictionary\Item;

class Railway extends Item
{
    public function getName()
    {
        return $this->dataDot->get('name');
    }
}
