<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels\components\columns;

use Travelpayouts\modules\tables\components\hotels\components\HotelButtonModel;

class ColumnButton extends \Travelpayouts\components\grid\columns\ColumnButton
{

    /**
     * @required
     * @var string|integer
     */
    protected $cityId;
    /**
     * @required
     * @var boolean
     */
    protected $withoutDates;
    /**
     * @required
     * @var string
     */
    protected $currency;
    /**
     * @var string
     * @required
     */
    protected $buttonModelAttribute;

    /**
     * @var string|null
     * @required
     */
    protected $subid;
    /**
     * @var string|null
     * @required
     */
    protected $linkMarker;
    /**
     * @var string|null
     */
    protected $locale;

    public function getButtonUrl($model): ?string
    {
        $buttonModel = $this->getButtonModel($model);
        if ($buttonModel) {
            $buttonModel->cityId = $this->cityId;
            $buttonModel->withoutDates = $this->withoutDates;
            $buttonModel->currency = $this->currency;
            $buttonModel->subid = $this->subid;
            $buttonModel->linkMarker = $this->linkMarker;
            $buttonModel->locale = $this->locale;
            return $buttonModel->getUrl();
        }
        return null;
    }

    protected function getButtonModel(object $model): ?HotelButtonModel
    {
        $value = $model->{$this->buttonModelAttribute};
        return $value instanceof HotelButtonModel ? $value : null;
    }

}
