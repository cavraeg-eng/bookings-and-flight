<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts;


use Travelpayouts\components\ApiModel;
use Travelpayouts\components\Translator;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\modules\tables\components\api\travelpayouts\citySuggest\CitySuggestItem;

/**
 * Class CitySuggestApiModel
 * @package Travelpayouts\modules\tables\components\api\travelpayouts
 * @property CitySuggestItem[]|null $response
 * @property-read CitySuggestItem|null $firstRecord
 */
class CitySuggestApiModel extends ApiModel
{
    public $service = 'internal_blissey_generator_ac';
    /**
     * @var string
     */
    public $term;
    /**
     * @var string
     */
    public $type = 'city';

    public $locale = Translator::DEFAULT_TRANSLATION;

    protected $cacheTime = 60 * 60 * 24 * 5;

    public function rules()
    {
        return [
            [
                [
                    'term',
                    'type',
                    'service',
                ],
                'required',
            ],
            [
                [
                    'type',
                    'locale',
                    'term',
                    'service',
                ],
                'string',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function request()
    {
        return $this->fetchApi();
    }

    protected function afterRequest()
    {
        $response = $this->response;
        if (is_array($response)) {
            $this->response = array_map(function ($responseItem) {
                $itemModel = new CitySuggestItem;
                $itemModel->attributes = array_merge($responseItem, ['locale' => $this->locale]);
                $itemModel->validate();
                return $itemModel;
            }, $response);
        } else {
            $this->response = null;
            $this->add_error('service', print_r($response, true));
        }
    }

    /**
     * @return CitySuggestItem|null
     */
    public function getFirstRecord()
    {
        return ArrayHelper::getFirst($this->response);
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'https://suggest.travelpayouts.com/search';
    }
}
