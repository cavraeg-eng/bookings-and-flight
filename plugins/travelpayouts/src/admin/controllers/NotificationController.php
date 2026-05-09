<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\controllers;

use Travelpayouts;
use Travelpayouts\components\exceptions\HttpException;
use Travelpayouts\components\notices\Notices;
use Travelpayouts\components\web\JsonController;
use Travelpayouts\components\web\Request;

class NotificationController extends JsonController
{
    /**
     * @Inject
     * @var Notices
     */
    public $notices;

    protected $routerPath = 'notifications';

    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'hide' => [
                'method' => 'PUT',
                'action' => [$this, 'actionHideNotification'],
                'checkAccess' => [$this, 'isUserCanManageOptions'],
            ],
            'disable' => [
                'method' => 'PUT',
                'action' => [$this, 'actionTurnOffNotification'],
                'checkAccess' => [$this, 'isUserCanManageOptions'],
            ],
        ];
    }

    public function actionHideNotification(): ?array
    {
        $notificationName = Request::getInstance()->getInputParam('name');
        if ($notificationName && is_string($notificationName)) {
            $this->notices->requestAlert($notificationName);
            return [
                'notificationId' => $notificationName,
            ];
        }
        throw new HttpException(403, Travelpayouts::__('Insufficient access rights!'));
    }

    public function actionTurnOffNotification(): ?array
    {
        $notificationName = Request::getInstance()->getInputParam('name');
        if ($notificationName && is_string($notificationName)) {
            $this->notices->disable($notificationName);
            return [
                'notificationId' => $notificationName,
            ];
        }
        throw new HttpException(403, Travelpayouts::__('Insufficient access rights!'));
    }

    public function isUserCanManageOptions(): bool
    {
        return TRAVELPAYOUTS_DEBUG || user_can(wp_get_current_user(), 'manage_options');
    }
}