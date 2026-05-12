<?php

namespace Travelpayouts\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\modules\account\Account;

class AirtableApiModel extends InjectedModel
{
    public $baseId;

    public $tableId;

    public $token;

    public $cacheTime = 60 * 60 * 2;

    /**
     * @Inject
     * @var Account
     */
    public $account;

    public function getRequestUrl(): string
    {
        return 'https://api.airtable.com/v0/' .
            implode('/', [$this->baseId, $this->tableId]) .
            '?' . http_build_query([
                'filterByFormula' => 'marker = "' . $this->account->marker . '"',
                'maxRecords' => 1,
            ]);
    }

    public function getResponse()
    {
        return $this->getHttpClient()->get($this->getRequestUrl())->json;
    }

    protected function getHttpClient(): CachedClient
    {
        return new CachedClient([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ]
        ], $this->cacheTime);
    }
}
