<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\base\dictionary\Dictionary;
use Exception;

class TravelpayoutsApiData extends Dictionary
{
    const CASE_NOMINATIVE = false; // именительный падеж
    const CASE_GENITIVE = 'ro'; // родительный
    const CASE_ACCUSATIVE = 'vi'; // винительный
    const CASE_DATIVE = 'da'; // дательный
    const CASE_INSTRUMENTAL = 'tv'; // творительный
    const CASE_PREPOSITIONAL = 'pr'; // предложный

    protected $_localesFallback = [
        'be' => 'ru',
        'bs' => 'hr',
        'ca' => 'es',
        'ce' => 'ru',
        'hy' => 'ru',
        'kk' => 'ru',
        'me' => 'sr',
        'tg' => 'ru',
        'uz' => 'ru',
    ];

    protected $_locales = [
        'ar',
        'bg',
        'cs',
        'da',
        'de',
        'el',
        'en',
        'en-AU',
        'en-CA',
        'en-GB',
        'en-IE',
        'en-IN',
        'en-NZ',
        'en-SG',
        'es',
        'fa',
        'fi',
        'fr',
        'he',
        'hi',
        'hr',
        'hu',
        'id',
        'it',
        'jp',
        'ka',
        'ko',
        'lt',
        'lv',
        'ms',
        'nl',
        'no',
        'pl',
        'pt',
        'pt-BR',
        'ro',
        'ru',
        'sk',
        'sl',
        'sr',
        'sv',
        'th',
        'tl',
        'tr',
        'uk',
        'vi',
        'zh-CN',
        'zh-Hans',
        'zh-Hant',
        'zh-TW',
    ];

    public function init()
    {
        parent::init();
        if (!$this->lang) throw new Exception("[{$this->className}]: lang property must be set");
    }

    /**
     * Get api data
     * @return array
     * @throws Exception
     */
    protected function fetchData()
    {
        if ($this->lang && $this->type) {
            $fileUrl = "https://api.travelpayouts.com/data/{$this->lang}/{$this->type}.json";
            $data = $this->getCache()->get($fileUrl);
            if ($data === false) {
                $response = $this->sendRequest('get', $fileUrl);
                if ($response) {
                    $data = $response->json;
                    $this->getCache()->set($fileUrl, $data, self::CACHE_TIME);

                }
            }
            return $data ? $data : [];
        }
        return [];
    }
}
