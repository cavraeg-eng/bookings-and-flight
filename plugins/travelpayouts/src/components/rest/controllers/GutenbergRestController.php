<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\controllers;

use Travelpayouts\components\rest\actions\EditShortcodeAction;
use Travelpayouts\components\rest\actions\GenerateShortcodeAction;
use Travelpayouts\components\rest\actions\GetAccessTokenAction;
use Travelpayouts\components\rest\actions\PreviewShortcodeAction;
use Travelpayouts\components\rest\models\GutenbergModulesModel;
use Travelpayouts\components\web\WpRestController;
use Travelpayouts\helpers\ArrayHelper;

class GutenbergRestController extends WpRestController
{
    public $routerPath = 'travelpayouts/widget';

    protected $shortcodes = [
        \Travelpayouts\modules\tables\components\flights\cheapestFlights\Table::class,
        \Travelpayouts\modules\tables\components\flights\cheapestTicketEachDayMonth\Table::class,
        \Travelpayouts\modules\tables\components\flights\cheapestTicketsEachMonth\Table::class,
        \Travelpayouts\modules\tables\components\flights\directFlights\Table::class,
        \Travelpayouts\modules\tables\components\flights\directFlightsRoute\Table::class,
        \Travelpayouts\modules\tables\components\flights\flightSchedule\Table::class,
        \Travelpayouts\modules\tables\components\flights\fromOurCityFly\Table::class,
        \Travelpayouts\modules\tables\components\flights\inOurCityFly\Table::class,
        \Travelpayouts\modules\tables\components\flights\ourSiteSearch\Table::class,
        \Travelpayouts\modules\tables\components\flights\popularDestinationsAirlines\Table::class,
        \Travelpayouts\modules\tables\components\flights\popularRoutesFromCity\Table::class,
        \Travelpayouts\modules\tables\components\flights\priceCalendarMonth\Table::class,
        \Travelpayouts\modules\tables\components\flights\priceCalendarWeek\Table::class,
        \Travelpayouts\modules\tables\components\hotels\selectionsDate\Table::class,
        \Travelpayouts\modules\tables\components\hotels\selectionsDiscount\Table::class,
        \Travelpayouts\modules\tables\components\railway\tutu\TutuShortcodeModel::class,
        \Travelpayouts\modules\searchForms\components\SearchFormShortcode::class,
        \Travelpayouts\modules\searchForms\components\SearchFormHotelsShortcode::class,
        \Travelpayouts\modules\links\components\flights\Shortcode::class,
        \Travelpayouts\modules\links\components\hotels\Shortcode::class,
    ];


    public function shortcodeActions()
    {
        $result = [];
        foreach ($this->shortcodes as $shortcodeClass) {
            if (method_exists($shortcodeClass, 'shortcodeTags')) {
                $tags = $shortcodeClass::shortcodeTags();
                foreach ($tags as $tag) {
                    $result["shortcode/$tag"] = [
                        'class' => GenerateShortcodeAction::class,
                        'modelClass' => $shortcodeClass,
                        'method' => 'POST',
                        'checkAccess' => [$this, 'isUserCanCreatePosts'],
                    ];
                }
            }
        }
        return $result;
    }

    public function actions()
    {
        return array_merge([
            'index' => [
                'method' => 'GET',
                'action' => [$this, 'actionIndex'],
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'module/tables' => [
                'method' => 'GET',
                'action' => [$this, 'actionTables'],
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'module/widgets' => [
                'method' => 'GET',
                'action' => [$this, 'actionWidgets'],
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'module/links' => [
                'method' => 'GET',
                'action' => [$this, 'actionLinks'],
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'previewShortcode' => [
                'class' => PreviewShortcodeAction::class,
                'shortcodeList' => $this->shortcodes,
                'method' => 'POST',
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'editShortcode'=>[
                'class'=> EditShortcodeAction::class,
                'shortcodeList' => $this->shortcodes,
                'method' => 'POST',
                'checkAccess' => [$this, 'isUserCanCreatePosts'],
            ],
            'token' => [
                'class' => GetAccessTokenAction::class,
                'method' => 'GET',
                'checkAccess' => [$this, 'isUserCanManageOptions'],
            ],
        ], $this->shortcodeActions());
    }

    public function actionIndex()
    {
        return ArrayHelper::toArray(new GutenbergModulesModel());
    }

    public function actionTables()
    {
        return ArrayHelper::toArray(new \Travelpayouts\modules\tables\components\GutenbergRestModule());
    }

    public function actionWidgets()
    {
        return ArrayHelper::toArray(new \Travelpayouts\modules\searchForms\SearchFormRestModule());
    }

    public function actionLinks()
    {
        return ArrayHelper::toArray(new \Travelpayouts\modules\links\components\GutenbergRestModule());
    }

    public function isUserCanCreatePosts()
    {
        return TRAVELPAYOUTS_DEBUG || user_can(wp_get_current_user(), 'publish_posts');
    }

    public function isUserCanManageOptions()
    {
        return TRAVELPAYOUTS_DEBUG || user_can(wp_get_current_user(), 'manage_options');
    }
}
