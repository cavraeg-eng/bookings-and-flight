<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;

use Travelpayouts\modules\tables\components\BaseTableFields;

/**
 * Class Fields
 * @package Travelpayouts\modules\tables\components\flights
 */
abstract class BaseFields extends BaseTableFields
{
    public function __construct(Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritDoc
     */
    public function dashboardColumnLabels(): array
    {
        return ColumnLabels::getInstance()
            ->getDashboardColumnLabels();
    }
}
