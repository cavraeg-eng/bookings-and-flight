<?php

namespace Travelpayouts\modules\searchForms;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\admin\redux\extensions\reimportSearchForms\ReimportSearchFormField;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\section\fields\Raw;
use Travelpayouts\includes\migrations\MigrationQuery;
use Travelpayouts\modules\searchForms\models\SearchFormModel;

class SearchFormSection extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Search forms'),
            'icon' => 'tp-i-tabler:device-tablet-search',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $importButton = [];

        if ((new MigrationQuery())->getSearchFormsCount()) {
            $importButton = [
                'id' => 'reimport_search_forms',
                'type' => ReimportSearchFormField::TYPE,
            ];
        }
        $fieldId = 'search_forms_data';

        return [
            'search_forms_component' => (new Raw())->setContent(HtmlHelper::tagArrayContent('div', [
                'style' => 'margin: -15px -10px;'
            ], HtmlHelper::reactWidget('TravelpayoutsSearchForms', [
                'outputSelector' => "#{$this->getId()}_{$fieldId}",
                'apiUrl' => admin_url('admin-ajax.php'),
            ]))),
            [
                'id' => $fieldId,
                'type' => 'textarea',
                'readonly' => 'true',
                'class' => 'hidden',
            ],
            $importButton,
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'search_forms';
    }

    public static function isActive(): bool
    {
        return TRAVELPAYOUTS_DEBUG || SearchFormModel::getInstance()->getTotalItemsCount() > 0;
    }
}
