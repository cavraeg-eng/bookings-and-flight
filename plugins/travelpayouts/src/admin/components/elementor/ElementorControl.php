<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components\elementor;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Elementor\Base_Data_Control;
use Travelpayouts\components\Assets;
use Travelpayouts\components\Container;
use Travelpayouts\components\HtmlHelper;

class ElementorControl extends Base_Data_Control
{
    const CONTROL_ID = 'travelpayouts_widget';
    /**
     * @Inject
     * @var Assets
     */
    protected $assets;

    public function __construct()
    {
        parent::__construct();
        Container::getInstance()->inject($this);

        $this->assets->getAssetByName('admin-gutenberg-modal')
            ->setInFooter(true)->enqueueScript()->enqueueStyle();
        $this->assets->getAssetByName('admin-elementor-injector')
            ->setInFooter(true)->enqueueScript()->enqueueStyle();

    }

    /**
     * @inheritDoc
     */
    public function get_type()
    {
        return self::CONTROL_ID;
    }

    /**
     * Render text control output in the editor.
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     * @since 1.0.0
     * @access public
     */
    public function content_template()
    {
        $control_uid = $this->get_control_uid();

        $this->renderInlineCode();
        echo HtmlHelper::input($control_uid, '', [
            'id' => $control_uid,
            'type' => 'text',
            'data-setting' => '{{ data.name }}',
        ]);
    }

    /**
     * Add event with updateValue function to catch it later with gutenberg react web component
     */
    protected function renderInlineCode()
    {
        $inlineJsCode = <<<JS
document.dispatchEvent(
    new CustomEvent('travelpayouts_widget_elementor_register', {
        detail: {
            updateValue: function(value) {
                view.updateElementModel(value);
            }
        }
    }));
JS;
        echo '<#' . $inlineJsCode . '#>';
    }

    public function enqueue()
    {
        $this->assets->getAssetByName('admin-gutenberg-modal')
            ->setInFooter(true)->enqueueScript()->enqueueStyle();
        $this->assets->getAssetByName('admin-elementor-injector')
            ->setInFooter(true)->enqueueScript()->enqueueStyle();
    }
}
