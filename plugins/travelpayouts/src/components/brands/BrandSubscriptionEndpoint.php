<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\brands;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

class BrandSubscriptionEndpoint extends BaseTravelpayoutsApiModel
{
    public $cacheTime = 2 * 60;

    /**
     * @var string
     */
    public $brand_id;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['brand_id'], 'required'],
            ['brand_id', 'number', 'integerOnly' => true, 'min' => 1],
        ]);
    }

    public function afterRequest()
    {
        $response = $this->response;
        if (is_array($response)) {
            if (isset($response['subscribed'])) {
                $this->response = $response['subscribed'];
            } else {
                $this->fetchErrors();
                $this->response = false;
            }
        } else {
            $this->response = false;
        }
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl(): string
    {
        return 'https://api.travelpayouts.com/brands/subscription';
    }
}