<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway;

use Travelpayouts\components\tables\TableShortcode;

abstract class RailwayShortcodeModel extends TableShortcode
{
    public $tableWrapperClassName = 'tp-widget-table-railway';
    public static function isActive(): bool
    {
      return Section::isActive();
    }
}
