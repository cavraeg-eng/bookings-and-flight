<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components\elementor;

use Travelpayouts\components\HookableObject;
use Travelpayouts\includes\HooksLoader;

class ElementorHooks extends HookableObject
{
    /**
     * @inheritDoc
     */
    protected function hookList(HooksLoader $hooksLoader)
    {
        $hooksLoader
            ->addAction('elementor/controls/controls_registered', [$this, 'registerControl'])
            ->addAction('elementor/widgets/widgets_registered', [$this, 'registerWidget'])
            ->addAdminAjaxEndpoint(PreviewController::ACTION_ID, [$this, 'registerAjaxEndpoint']);
    }

    /**
     * @param \Elementor\Widgets_Manager $manager
     */
    public function registerWidget($manager)
    {
        if ($this->isElementorExists()) {
            $manager->register_widget_type(new ElementorWidget());
        }
    }

    /**
     * @param \Elementor\Controls_Manager $manager
     */
    public function registerControl($manager)
    {
        if ($this->isElementorExists()) {
            $manager->register_control(ElementorControl::CONTROL_ID, new ElementorControl());
        }
    }

    public function registerAjaxEndpoint()
    {
        if ($this->isElementorExists()) {
            $controller = new PreviewController();
            $controller->run();
        }
    }

    /**
     * @return bool
     */
    protected function isElementorExists()
    {
        return class_exists('Elementor\\Widget_Base');
    }
}
