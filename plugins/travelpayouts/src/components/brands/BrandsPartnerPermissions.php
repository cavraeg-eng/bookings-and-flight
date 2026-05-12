<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\brands;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\api\ApiEndpoint;
use Travelpayouts\components\api\ApiResponseObject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\httpClient\CachedResponse;
use Travelpayouts\modules\account\AccountForm;

class BrandsPartnerPermissions extends ApiEndpoint
{
    /**
     * @Inject
     * @var AccountForm
     */
    protected $account;

    /**
     * @var int
     */
    public $brand_id;

    public function init()
    {
        BaseInjectedObject::inject($this);
    }

    protected function clientOptions(): array
    {
        return [
            'headers' => [
                'X-Access-Token' => $this->account->api_token,
            ],
        ];
    }

    public function getResponse(): ?CachedResponse
    {
        return $this->getClient()->get('https://api.travelpayouts.com/brands/partner_permissions', [
            'query' => [
                'brand_id' => $this->brand_id,
            ],
        ]);
    }

    public function getData(): ?BrandsPartnerPermissionsResponse
    {
        $response = $this->getResponse();
        return $response && $response->getIsSuccess()
            ? BrandsPartnerPermissionsResponse::createFromArray($response->json)
            : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRule(string $name): bool
    {
        $data = $this->getData();
        return $data && $data->hasRule($name);
    }

}

class BrandsPartnerPermissionsResponse extends ApiResponseObject
{
    /**
     * @var string[]
     */
    public $rules = [];

    public function hasRule(string $name): bool
    {
        return in_array($name, $this->rules);
    }

}