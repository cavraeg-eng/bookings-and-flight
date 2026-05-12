<?php

namespace Travelpayouts\modules\tables\components\columnTitles;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\Controller as BaseController;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\components\tables\BaseColumnLabels;
use Travelpayouts\components\Translator;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\modules\tables\components\flights\ColumnLabels as FlightLabels;
use Travelpayouts\modules\tables\components\hotels\ColumnLabels as HotelLabels;
use Travelpayouts\modules\tables\components\railway\ColumnLabels as RailwayLabels;

class Controller extends BaseController
{
    /**
     * @Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @var Section
     */
    protected $section;

    public function init()
    {
        if (!$this->section) {
            throw new \Exception('Section property must be set');
        }
    }

    public function actionTranslationPhrases()
    {
        $localeId = $this->getQueryParam('locale');

        if ($localeId) {
            $supportedLocales = $this->translator->getLocaleNames();
            $isHoteLookSubscribed = BrandSubscriptionService::isHotelLookAvailable();

            if (isset($supportedLocales[$localeId])) {
                $this->response(true, [
                    [
                        'label' => Travelpayouts::__('Flights'),
                        'data' => $this->getColumnLabelsByLocaleName(FlightLabels::getInstance(), $localeId),
                    ],
                    [
                        'label' => Travelpayouts::__('Hotels column titles'),
                        'data' => $isHoteLookSubscribed ?
                            $this->getColumnLabelsByLocaleName(HotelLabels::getInstance(), $localeId)
                            : [],
                    ],
                    [
                        'label' => Travelpayouts::__('Railways column titles'),
                        'data' => $this->getColumnLabelsByLocaleName(RailwayLabels::getInstance(), $localeId),
                    ],
                ]);
            }
        }
        $this->response(false);
    }

    /**
     * @param BaseColumnLabels $labelsInstance
     * @param $localeName
     * @return array<string,array{id:string,placeholder: string,labels: array<int,string>}>
     */
    protected function getColumnLabelsByLocaleName(BaseColumnLabels $labelsInstance, $localeName)
    {
        $result = [];
        // Заголовки таблиц по умолчанию
        $defaultTranslations = $labelsInstance->defaultTranslations();
        $groupedColumnNames = $this->groupColumnLabelsByTranslationKey($labelsInstance);
        // Заголовки таблиц полученные из symfony translator
        $customTranslations = $labelsInstance->getColumnLabels(null, $localeName);

        foreach ($groupedColumnNames as $translationKey => $columnNames) {
            $columnLabels = $this->getColumnLabels($defaultTranslations, $columnNames);
            if (!empty($columnLabels)) {
                $columnName = ArrayHelper::getFirst($columnNames);
                $result[] = [
                    'labels' => array_values($columnLabels),
                    'id' => $translationKey,
                    'placeholder' => $customTranslations[$columnName],
                ];
            }
        }
        return $result;
    }

    /**
     * Объединяем колонки с одинаковыми названиями ключей для перевода
     * @param BaseColumnLabels $labelsInstance
     * @return array<string, array<int,string>>
     */
    protected function groupColumnLabelsByTranslationKey(BaseColumnLabels $labelsInstance)
    {
        $result = [];

        foreach ($labelsInstance->translationKeys() as $columnName => $translationKey) {
            $result[$translationKey][] = $columnName;
        }
        return $result;
    }

    /**
     * Получаем дефолтные заголовки колонок
     * @param string[] $defaultTranslations
     * @param string[] $columnNames
     * @return array
     */
    protected function getColumnLabels($defaultTranslations, $columnNames)
    {
        $result = [];

        foreach ($columnNames as $columnName) {
            if (isset($defaultTranslations[$columnName])) {
                $columnLabel = $defaultTranslations[$columnName];
                // пропускаем колонки с одинаковыми названиями
                if (!in_array($columnLabel, $result, true)) {
                    $result[$columnName] = $columnLabel;
                }
            }
        }
        return $result;
    }

    public function actionGetData()
    {
        $this->response(true,
            [
                'i18n' => $this->translations(),
                'availableLocales' => $this->getAvailableLocales(),
                'translatedPhrases' => $this->section->translatedPhrases,
                'locale' => LanguageHelper::tableLocale(),
            ]
        );

    }

    /**
     * @return array
     */
    protected function translations()
    {
        return [
            'locale_select.title' => Travelpayouts::_x('Select language you want to customize', 'tables.columnTitles'),
            'locale_select.placeholder' => Travelpayouts::_x('Select languages...', 'tables.columnTitles'),
            'phrase.has_synonyms' => Travelpayouts::_x('This phrase also used for columns', 'tables.columnTitles'),
        ];
    }

    /**
     * @return array
     */
    protected function getAvailableLocales()
    {
        $languages = $this->translator->getLocaleNames();
        $result = [];
        foreach ($languages as $value => $label) {
            $result[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $result;
    }

}
