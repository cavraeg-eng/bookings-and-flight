<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\railway\components\columns;

use Travelpayouts\modules\tables\components\railway\components\RailwayButtonModel;

class ColumnButton extends \Travelpayouts\components\grid\columns\ColumnButton
{
    /**
     * @required
     * @var string
     */
    protected $origin;
    /**
     * @required
     * @var string
     */
    protected $destination;
    /**
     * @required
     * @var string
     */
    protected $subid;
    /**
     * @required
     * @var string|null
     */
    protected $linkMarker;

    public function getButtonUrl($model): ?string
    {
        return $this->getButtonModel()->getUrl();
    }

    protected function getButtonModel()
    {
        $model = new RailwayButtonModel();
        $model->linkMarker = $this->linkMarker;
        $model->subid = $this->subid;
        $model->origin = $this->origin;
        $model->destination = $this->destination;

        return $model;
    }

}
