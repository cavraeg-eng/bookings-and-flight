<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\components;

use Travelpayouts;
use Travelpayouts\modules\searchForms\models\SearchFormModel;

class SearchFormHotelsShortcode extends SearchFormShortcode
{
    /**
     * @inheritDoc
     */
    public function shortcodeName(): string
    {
        return Travelpayouts::__('Hotels search form');
    }

    /**
     * @inheritDoc
     */
    public function gutenbergExtraData(): array
    {
        return [
            'image' => Travelpayouts::getAlias('@webImages/rest/search_form_hotels.png'),
        ];
    }

    public function gutenbergFields(): array
    {
        $model = new SearchFormModel();
        $options = $model->selectData($model->getHotelsForms());

        return [
            'id' => $this->fieldSelect()
                ->setLabel(Travelpayouts::__('Select search form'))
                ->setOptions($options)
                ->required(),
            'hotel_city',
            'applyParamsFromCode',
            'subid',
        ];
    }
}
