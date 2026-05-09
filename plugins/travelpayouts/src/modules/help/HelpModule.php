<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\help;
use Travelpayouts\Vendor\DI\Annotation\Inject;

class HelpModule extends \Travelpayouts\components\module\ModuleRedux
{
    /**
     * @Inject
     * @var HelpSection
     */
    public $section;

    /**
     * @inheritDoc
     */
    public function registerSection()
    {
        $this->section->register();
    }
}
