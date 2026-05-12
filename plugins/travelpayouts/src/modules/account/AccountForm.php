<?php

namespace Travelpayouts\modules\account;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\brands\BrandSubscriptionService;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\brands\PlatformsEndpoint;

class AccountForm extends ModuleSection
{
    /**
     * @var string
     */
    public $api_token;
    /**
     * @var string
     */
    public $api_marker;
    /**
     * @var string
     */
    public $platform;
    /**
     * @var string
     */
    public $flights_domain;
    /**
     * @var string
     */
    public $hotels_domain;
    /**
     * @var string
     */
    public $wl_domain;

    /**
     * @inheritdoc
     */
    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Account'),
            'icon' => 'tp-i-tabler:user-cog',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $isSubscribedToHotels = BrandSubscriptionService::isHotelLookAvailable();

        return [
            'api_token' => $this->fieldInput()
                ->setTitle(Travelpayouts::__('Your Travelpayouts API token'))
                ->setSubtitle('<a href="https://app.travelpayouts.com/profile/api-token" target="_blank" class="tp-link">' . Travelpayouts::__('Get API token and affiliate ID') . '</a>')
                ->setDesc(Travelpayouts::__('Enter your API token')),
            'api_marker' => $this->fieldInput()
                ->setTitle(Travelpayouts::__('Your Travelpayouts Partner ID'))
                ->setDesc(Travelpayouts::__('Enter your Partner ID')),
            'platform' => $this->fieldTrafficSource()
                ->setTitle(Travelpayouts::__('Projects'))
                ->setSubtitle(HtmlHelper::tag(
                        'p',
                        [],
                        Travelpayouts::__('Select your relevant channel')
                    ) . HtmlHelper::tag(
                        'p',
                        [],
                        Travelpayouts::__('<a href="https://support.travelpayouts.com/hc/en-us/articles/360015015439-Traffic-sources" target="_blank" class="tp-link">What are Projects?</a>')
                    ))
                ->setDesc(Travelpayouts::__('Select Project with your channel, where you use brands’ affiliate tools. This enables an accurate tracking of your views, clicks and other performance stats. Create and edit Projects through your account on <a href="https://www.travelpayouts.com/profile/sources" class="tp-link" target="_blank">Travelpayouts.com</a>'))
                ->setOptions(PlatformsEndpoint::getInstance()->getSelectOptions()),
            'wl_domain_start' => $this->fieldSection()
                ->setTitle(Travelpayouts::__('Your White Label domain'))
                ->setSubtitle(Travelpayouts::__('The domain name you entered in your White Label settings in Travelpayouts')),
            'flights_domain' => $this->fieldInput()
                ->setTitle(Travelpayouts::__('White Label with flights'))
                ->setDesc(Travelpayouts::__('Ensure your White Label’s URL contains /flights after the  domain name. Example: "mywhitelabel.com/flights"')),
            'hotels_domain' => $this->fieldInput()
                ->setTitle(Travelpayouts::__('White Label with hotels'))
                ->setHidden(!$isSubscribedToHotels)
                ->setDesc(Travelpayouts::__('Ensure your White Label’s URL contains /hotels after the  domain name. Example: "mywhitelabel.com/hotels"')),
            'wl_domain_end' => $this->fieldSection(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'account';
    }
}
