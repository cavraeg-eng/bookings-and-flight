<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components;


interface IBaseTableFields
{
    /**
     * @param null $locale
     * @return string
     */
    public function titlePlaceholder($locale = null);

    /**
     * @param null $locale
     * @return string
     */
    public function buttonPlaceholder($locale = null);
}
