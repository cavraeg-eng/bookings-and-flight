<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\widgets;

use Travelpayouts\components\BaseWidget;

class AlertWidget extends BaseWidget
{
    public const TYPE_ERROR = 'danger';
    public const TYPE_WARNING = 'warning';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public $content = '';
    public $title = null;
    public $type = self::TYPE_INFO;

    public $showRoundel = true;

    public function getAlertType(): string
    {
        $validTypes = [
            self::TYPE_ERROR,
            self::TYPE_WARNING,
            self::TYPE_INFO,
            self::TYPE_SUCCESS,
        ];

        return in_array($this->type, $validTypes) ? $this->type : self::TYPE_INFO;
    }

    public function init()
    {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    public function run(): string
    {
        $content = ob_get_clean();
        if (is_string($content) && trim($content) !== '') {
            $this->content = $content;
        }
        if ($this->content === '') {
            return '';
        }
        return $this->render('alert');
    }

}