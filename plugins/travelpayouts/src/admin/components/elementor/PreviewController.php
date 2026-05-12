<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\components\elementor;

use Travelpayouts\components\Component;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\helpers\StringHelper;

/**
 * Class ElementorPreviewController
 * @package Travelpayouts\admin\controllers
 */
class PreviewController extends Component
{
    const ACTION_ID = 'travelpayouts_elementor_preview';

    public function run()
    {
        $scriptUrl = $this->getScriptUrl();
        if ($scriptUrl) {
            check_admin_referer('travelpayouts_elementor_' . md5($_GET['externalUrl']), 'wpnonce');
            $scriptUrl = do_shortcode(($scriptUrl));
            echo HtmlHelper::tag('div', ['class' => 'tp-shortcode'], $scriptUrl);
        }
        wp_die();
    }

    public function getScriptUrl()
    {
        $script = isset($_GET['externalUrl'])
            ? $_GET['externalUrl']
            : false;
        return $script
            ? StringHelper::base64UrlDecode($script)
            : null;
    }

    public static function generateNonce($data)
    {
        return wp_create_nonce('travelpayouts_elementor_' . md5(StringHelper::base64UrlEncode($data)));
    }
}
