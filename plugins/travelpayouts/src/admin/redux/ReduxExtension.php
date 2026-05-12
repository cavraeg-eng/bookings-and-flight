<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux;

use Redux_Travelpayouts_Extension_Abstract;
use Travelpayouts\admin\redux\extensions\clearTableCache\ClearTableCacheField;
use Travelpayouts\admin\redux\extensions\OscAccordionField;
use Travelpayouts\admin\redux\extensions\platformSelect\PlatformSelectField;
use Travelpayouts\admin\redux\extensions\reimportSearchForms\ReimportSearchFormField;
use Travelpayouts\admin\redux\extensions\SettingsImportField;
use Travelpayouts\admin\redux\extensions\sortBy\SortByField;
use Travelpayouts\admin\redux\extensions\sorter\SorterField;

class ReduxExtension extends Redux_Travelpayouts_Extension_Abstract
{
    public function init(): void
    {
        $this->addPsr4Field(OscAccordionField::class, OscAccordionField::TYPE);
        $this->addPsr4Field(ClearTableCacheField::class, ClearTableCacheField::TYPE);
        $this->addPsr4Field(ReimportSearchFormField::class, ReimportSearchFormField::TYPE);
        $this->addPsr4Field(SettingsImportField::class, SettingsImportField::TYPE);
        $this->addPsr4Field(SorterField::class, SorterField::TYPE);
        $this->addPsr4Field(SortByField::class, SortByField::TYPE);
        $this->addPsr4Field(PlatformSelectField::class, PlatformSelectField::TYPE);
        // deprecated fields
//        $this->addPsr4Field(AutocompleteField::class, AutocompleteField::TYPE);
    }
}