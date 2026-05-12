<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

use Travelpayouts\modules;
use function Travelpayouts\Vendor\DI\autowire;

return [
    modules\tables\Tables::class => autowire(),
    modules\widgets\Widgets::class => autowire(),
    modules\searchForms\SearchFormModule::class => autowire(),
    modules\moneyScript\MoneyScriptModule::class => autowire(),
    modules\account\Account::class => autowire(),
    modules\settings\Settings::class => autowire(),
    modules\links\Links::class => autowire(),
    modules\help\HelpModule::class => autowire(),
];
