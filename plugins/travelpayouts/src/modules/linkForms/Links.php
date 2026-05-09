<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\linkForms;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\module\ModuleRedux;

/**
 * Class Links
 * @package Travelpayouts\src\modules\linkForms
 */
class Links extends ModuleRedux
{
    /**
     * @Inject
     * @var LinksForm
     */
    public $section;

    public function registerSection()
    {
        $this->section->register();
    }
}
