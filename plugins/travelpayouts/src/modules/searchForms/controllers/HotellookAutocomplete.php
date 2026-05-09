<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\searchForms\controllers;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\base\cache\Cache;
use Travelpayouts\components\Controller;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\components\LanguageHelper;
use Travelpayouts\modules\searchForms\models\hotellook\HotellookAutocompleteCity;
use Travelpayouts\modules\searchForms\models\hotellook\HotellookAutocompleteHotel;

class HotellookAutocomplete extends Controller
{
    /**
     * @Inject
     * @var Cache
     */
    protected $cache;
    /**
     * @Inject
     * @var CachedClient
     */
    protected $client;

    public function init()
    {
        $this->client->setCacheTime(7 * 60 * 60 * 24);
    }

    public function actionIndex()
    {
        $term = $this->getQueryParam('term');
        $language = $this->getQueryParam('lang', LanguageHelper::dashboardLocale());
        /** @var array{label:string,value:string} $response */
        $result = [];
        if ($term) {
            $response = $this->client->get("https://yasen.hotellook.com/autocomplete?term=$term&lang=$language");
            if (!$response->isError && $data = $response->json) {
                if (isset($data['cities'])) {
                    foreach ($data['cities'] as $city) {
                        $model = new HotellookAutocompleteCity($city);
                        $result[] = [
                            'label' => $model->label,
                            'value' => $model->getSearchFormShortcodeValue(),
                        ];
                    }
                }

                if (isset($data['hotels'])) {
                    foreach ($data['hotels'] as $city) {
                        $model = new HotellookAutocompleteHotel($city);
                        $result[] = [
                            'label' => $model->label,
                            'value' => $model->getSearchFormShortcodeValue(),
                        ];
                    }
                }
            }
        }
        return $this->response(true, $result);
    }

}
