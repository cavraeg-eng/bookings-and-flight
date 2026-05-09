<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;
use Travelpayouts\Vendor\League\Plates\Engine;
use Travelpayouts\components\exceptions\TravelpayoutsException;

class ExceptionRenderer
{
    /**
     * @var Engine
     */
    protected $plates;

    /**
     * @var TravelpayoutsException
     */
    protected $exception;

    /**
     * @var bool
     */
    protected $isAdmin = false;

    public function __construct(TravelpayoutsException $exception)
    {
        require ABSPATH . WPINC . '/pluggable.php';
        wp_cookie_constants();
        $this->isAdmin = user_can(wp_get_current_user(), 'manage_options');
        $this->plates = new Engine(TRAVELPAYOUTS_PLUGIN_PATH . '/src/components/web/views');
        $this->exception = $exception;
    }

    public function render()
    {
        wp_die($this->plates->render('error',
            [
                'error' => $this->exception,
                'version' => TRAVELPAYOUTS_VERSION,
                'isAdmin'=> $this->isAdmin,
            ]
        ));
    }
}
