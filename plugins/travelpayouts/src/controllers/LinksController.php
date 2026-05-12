<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\controllers;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\exceptions\BadRequestHttpException;
use Travelpayouts\components\snowplow\Tracker;
use Travelpayouts\components\web\PublicJsonController;
use Travelpayouts\components\web\Request;
use Travelpayouts\modules\settings\SettingsForm;

class LinksController extends PublicJsonController
{
    public $routerPath = 'links';

    /**
     * @Inject
     * @var Tracker
     */
    public $snowTracker;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $settings;

    public function actions()
    {
        return [
            'linkReplacementCount' => [
                'method' => 'POST',
                'action' => [$this, 'actionLinkReplacementStatistics'],
            ],
        ];
    }

    public function actionLinkReplacementStatistics()
    {
        if(!$this->settings->getUseFilterRef()){
            return false;
        }

        $url = Request::getInstance()->getInputParam('url');
        $linksCount = (int)Request::getInstance()->getInputParam('count', 0);
        if ($url && $linksCount && is_string($url) && preg_match('/^(https?:\/\/)/', $url)) {
            $value = get_transient($url);
            if ((int)$value !== $linksCount) {
                $this->snowTracker->trackStructEvent(
                    'Tools',
                    'links_installed',
                    null,
                    null,
                    null,
                    [
                        'page_url' => $url,
                        'number_of_links' => $linksCount,
                    ]
                );
                set_transient($url, $linksCount, MONTH_IN_SECONDS);
                return true;
            }
            return false;
        }
        throw new BadRequestHttpException('Invalid arguments passed');
    }
}
