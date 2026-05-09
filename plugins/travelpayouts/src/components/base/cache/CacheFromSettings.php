<?php

namespace Travelpayouts\components\base\cache;

use Travelpayouts\components\DisabledRedux;
use Travelpayouts\admin\redux\ReduxOptions;

class CacheFromSettings
{
    /**
     * @return string
     */
    public static function getCacheClass()
    {
        if (DisabledRedux::getOption('settings_use_fileCache', false)) {
            return FileCache::class;
        }

        return TransientCache::class;
    }
}