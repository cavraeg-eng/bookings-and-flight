<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\dictionary;

use Travelpayouts\components\dictionary\items\Campaign;
use Travelpayouts\components\base\dictionary\EmptyItem;
use Travelpayouts\components\base\dictionary\Dictionary;

/**
 * Class Campaigns
 * @package Travelpayouts\components\dictionary
 * @method Campaign|EmptyItem getItem(string $id)
 * @method static self getInstance()
 */
class Campaigns extends Dictionary
{
    public $_pk = 'campaign_id';
    public $itemClass = Campaign::class;

    protected function fetchData()
    {
        $fileUrl = 'https://misc.travelpayouts.com/tp_chrome_extension/config2.json';
        $data = $this->getCache()->get($fileUrl);
        if ($data === false) {
            $client = $this->getHttpClient();
            if ($client) {
                $response = $client->get($fileUrl);
                if (!$response->isError && $response->statusCode === 200 && $response->json) {
                    $data = $response->json;
                    $this->getCache()->set($fileUrl, $data, self::CACHE_TIME);
                }
            }
        }
        return $data ? $data : [];
    }
}
