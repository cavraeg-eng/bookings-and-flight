<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\widgets;


use Travelpayouts\admin\redux\base\SectionFields;

interface IWidgetModel
{
    /**
     * @param SectionFields $value
     * @return mixed
     */
    public function setSection($value);
}
