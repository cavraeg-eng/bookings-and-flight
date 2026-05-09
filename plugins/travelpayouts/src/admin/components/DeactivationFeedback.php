<?php

namespace Travelpayouts\admin\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\Vendor\League\Plates\Engine;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\LanguageHelper;

class DeactivationFeedback extends BaseInjectedObject
{
    const EN_FORM_ID = 'qgiWODqO';
    const RU_FORM_ID = 'zJGtoYPO';

    /**
     * @Inject
     * @var Engine
     */
    public $template;

    public function printModal()
    {
        echo $this->template->render('admin::deactivationFeedback/modal', [
            'isRU' => LanguageHelper::isRuDashboard(),
        ]);
    }

    public function run()
    {
        $currentScreen = get_current_screen();
        if ($currentScreen && in_array($currentScreen->id, ['plugins', 'plugins-network'])) {
            add_action('admin_footer', [$this, 'printModal']);
        }
    }
}
