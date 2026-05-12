<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components\elementor;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Travelpayouts;
use Travelpayouts\components\Assets;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\helpers\StringHelper;

class ElementorWidget extends Widget_Base
{
    use Travelpayouts\traits\SingletonTrait;

    /**
     * @Inject
     * @var Assets
     */
    protected $assets;

    /**
     * @inheritDoc
     */
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        Travelpayouts\components\Container::getInstance()->inject($this);
    }

    public static function register()
    {
        Plugin::instance()->widgets_manager->register_widget_type(new static());
    }

    /**
     * @inheritDoc
     */
    public function get_name()
    {
        return TRAVELPAYOUTS_PLUGIN_NAME . '_shortcode_widget';
    }

    /**
     * @inheritDoc
     */
    public function get_title()
    {
        return Travelpayouts::__('Travelpayouts shortcodes');
    }

    /**
     * @inheritDoc
     */
    public function get_icon()
    {
        return 'travelpayouts-elementor-button';
    }

    /**
     * @inheritDoc
     */
    public function get_categories()
    {
        return ['general'];
    }

    /**
     * @inheritDoc
     */
    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_content',
            [
                'label' => Travelpayouts::__('Settings'),
            ]
        );

        $this->add_control(
            'content',
            [
                'label' => Travelpayouts::__('Content'),
                'type' => ElementorControl::CONTROL_ID,
            ]
        );
        $this->end_controls_section();
    }

    /**
     * Render shortcode widget output on the frontend.
     * Written in PHP and used to generate the final HTML.
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $shortcode = $this->getContent();

        if (preg_match('/(script|iframe|src)/', $shortcode)) {
            $this->renderExternalWidget($shortcode);
        } else {
            $this->renderInternalWidget($shortcode);
        }
    }

    /**
     * @return string
     */
    protected function getContent()
    {
        return $this->get_settings_for_display('content');
    }

    protected function renderInternalWidget($content)
    {
        $shortcode = do_shortcode(shortcode_unautop($content));
        echo HtmlHelper::tag('div', [], $shortcode);
    }

    protected function renderExternalWidget($shortcode)
    {
        $iframeSource = admin_url('admin-ajax.php?' . http_build_query([
                'action' => PreviewController::ACTION_ID,
                'externalUrl' => StringHelper::base64UrlEncode($shortcode),
                'wpnonce' => PreviewController::generateNonce($shortcode),
            ]));
        echo HtmlHelper::tag('iframe', ['src' => $iframeSource, 'height' => 350]);
    }

    /**
     * Render shortcode widget as plain content.
     * Override the default behavior by printing the shortcode instead of rendering it.
     * @since 1.0.0
     * @access public
     */
    public function render_plain_content()
    {
        echo $this->get_settings('content');
    }

    /**
     * Render shortcode widget output in the editor.
     * Written as a Backbone JavaScript template and used to generate the live preview.
     * @since 2.9.0
     * @access protected
     */
    public function content_template()
    {
    }
}
