<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;


interface IModuleSection extends IModuleSectionFields
{
    /**
     * @return array
     */
    public function section(): array;
}
