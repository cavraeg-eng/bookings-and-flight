<?php

namespace Travelpayouts\modules\tables\components\columnTitles;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Exception;
use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\section\fields\Raw;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\includes\Router;
use Travelpayouts\modules\tables\components\Section as TablesSection;

class Section extends ModuleSection
{
    /**
     * @Inject
     * @var Router
     */
    protected $router;

    /**
     * @Inject
     * @var Travelpayouts\components\Translator
     */
    protected $translator;
    /**
     * array<string,array<string,string>
     * @var array
     */
    public $translatedPhrases = [];
    /**
     * @var string
     */
    public $translation_strings;



    public function __construct(TablesSection $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }


    public function init()
    {
        parent::init();
        $this->setUpRoutes();
        $this->setTranslationStrings($this->translation_strings);
    }


    /**
     * @param string|mixed $value
     * @return void
     */
    public function setTranslationStrings($value): void
    {
        if (is_string($value)) {
            try {
                $this->translatedPhrases = json_decode($value, true);
                $this->translator->addArrayTranslations($this->translatedPhrases, 'tables');
            } catch (Exception $e) {
            }
        }
    }

    /**
     * @return void
     */
    protected function setUpRoutes()
    {
        $controller = new Controller(['section' => $this]);
        $this->router->addRoutes([
            ['GET', 'columnTitles/translationPhrases', [$controller, 'actionTranslationPhrases']],
            [
                'GET',
                'columnTitles/data',
                [$controller, 'actionGetData'],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Fields\' labels'),
            'icon' => 'tp-i-tabler:language',
        ];
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        $fieldId = 'translation_strings';

        return [
            [
                'id' => $fieldId,
                'type' => 'textarea',
                'readonly' => 'true',
                'class' => 'hidden',
            ],
            'travelpayouts_multiselect_react' => (new Raw())->setContent(
                HtmlHelper::reactWidget('TravelpayoutsColumnTitles', [
                    'outputSelector' => "#{$this->getOptionPath()}_{$fieldId}",
                    'apiUrl' => admin_url('admin-ajax.php'),
                ])
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'column_titles';
    }
}
