<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\dictionary\Campaigns;

class Section extends ModuleSection
{
    public function __construct(Travelpayouts\modules\tables\components\Section $parent, $config = [])
    {
        parent::__construct($parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Flights'),
            'icon' => 'tp-i-tabler:plane',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'flights';
    }
}
