<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\includes\Router;
use Travelpayouts\modules\searchForms\controllers\HotellookAutocomplete;
use Travelpayouts\modules\searchForms\controllers\SearchFormsController;

/**
 * Class Search
 * @package Travelpayouts\src\modules\searchForms
 * @property-read SearchFormSection $section
 * @property-read Dot $data
 */
class SearchFormModule extends ModuleRedux
{
    /**
     * @Inject
     * @var SearchFormSection
     */
    public $section;

    /**
     * @Inject
     * @var Router
     */
    protected $router;
    /**
     * @Inject
     * @var \Travelpayouts\modules\searchForms\controllers\SearchFormsController
     */
    protected $controller;
    /**
     * @inheritdoc
     */
    protected $shortcodeList = [
        components\SearchFormShortcode::class,
    ];

    public function init()
    {
        $this->setUpRoutes();
    }

    protected function setUpRoutes()
    {
        $searchFormsController = new SearchFormsController();
        $this->router->addRoutes([
            ['GET', 'searchForms/raw-data', [$searchFormsController, 'actionRawData']],
            ['GET', 'searchForms', [$searchFormsController, 'actionIndex']],
            ['GET', 'searchForms/index', [$searchFormsController, 'actionIndex']],
            ['POST', 'searchForms/create', [$searchFormsController, 'actionCreate']],
            ['GET', 'searchForms/view/{id:\d+}', [$searchFormsController, 'actionView']],
            ['GET', 'searchForms/slug/{slug:\w+}', [$searchFormsController, 'actionViewBySlug']],
            ['PUT', 'searchForms/update/{id:\d+}', [$searchFormsController, 'actionUpdate']],
            ['PUT', 'searchForms/delete', [$searchFormsController, 'actionDeleteById']],
            ['DELETE', 'searchForms/delete/{id:\d+}', [$searchFormsController, 'actionDelete']],
            ['GET', 'searchForms/translations', [$searchFormsController, 'actionGetTranslations']],
            ['GET', 'hotellook/hotels-cities/autocomplete', [HotellookAutocomplete::getInstance(), 'actionIndex']],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function registerSection()
    {
        $this->section->register();
    }

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }
}
