<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;

class Section extends ModuleSection
{
    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Widgets'),
            'icon' => 'tp-i-tabler:apps',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'widgets';
    }
}
