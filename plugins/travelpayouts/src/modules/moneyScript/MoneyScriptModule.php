<?php

namespace Travelpayouts\modules\moneyScript;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\modules\moneyScript\MoneyScriptSection;

/**
 * Class MoneyScript
 * @property-read Dot $data
 * @package Travelpayouts\modules\moneyScript
 */
class MoneyScriptModule extends ModuleRedux
{
    /**
     * @Inject
     * @var MoneyScriptSection
     */
    public $section;

    public function registerSection()
    {
        $this->section->register();
    }

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }
}
