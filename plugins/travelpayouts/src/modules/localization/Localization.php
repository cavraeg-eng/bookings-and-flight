<?php

namespace Travelpayouts\modules\localization;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\module\ModuleRedux;

/**
 * Class Settings
 * @package Travelpayouts\src\modules\settings
 * @property-read Dot $data
 */
class Localization extends ModuleRedux
{
    /**
     * @Inject
     * @var LocalizationForm
     */
    public $section;

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }

    public function registerSection()
    {
        $this->section->register();
    }
}
