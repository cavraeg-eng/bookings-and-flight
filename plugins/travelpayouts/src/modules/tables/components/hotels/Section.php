<?php

/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\hotels;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\brands\CampaignsSubscriptionsEndpoint;
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
            'title' => Travelpayouts::__('Hotels'),
            'icon' => 'el el-home'
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'hotels';
    }

    public static function isActive(): bool
    {
        return BrandSubscriptionService::isHotelLookAvailable();
    }
}
