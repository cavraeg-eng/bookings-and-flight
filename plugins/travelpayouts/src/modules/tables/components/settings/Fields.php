<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts\admin\redux\base\SectionFields;

abstract class Fields extends SectionFields
{
    public function __construct(SettingsSection $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }
}
