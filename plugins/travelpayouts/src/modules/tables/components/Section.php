<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components;

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
            'title' => Travelpayouts::__('Tables'),
            'icon' => 'tp-i-tabler:table',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'tables';
    }
}
