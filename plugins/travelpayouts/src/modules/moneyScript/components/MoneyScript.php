<?php

namespace Travelpayouts\modules\moneyScript\components;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\modules\account\AccountForm;
use Travelpayouts\modules\moneyScript\MoneyScriptSection;

class MoneyScript extends BaseInjectedObject
{
    const SCRIPT_URL = 'www.travelpayouts.com/money_script/money_script.js';

    /**
     * @Inject
     * @var MoneyScriptSection
     */
    protected $moneyScriptSection;

    /**
     * @Inject
     * @var AccountForm
     */
    protected $accountSection;

    public function run()
    {
        if ($this->isActive()) {
            echo $this->script();
        }
    }

    /**
     * @return bool
     */
    protected function isActive()
    {
        return $this->moneyScriptSection->getIsActive();
    }

    /**
     * @return string
     */
    protected function script()
    {
        $marker = $this->accountSection->api_marker;
        if (empty($marker)) {
            return '';
        }

        $params = [
            'marker' => $marker,
            'exclude' => implode(',', $this->moneyScriptSection->getExcludedCampaignIds()),
        ];

        return HtmlHelper::scriptFile(
            UrlHelper::buildUrl(
                self::SCRIPT_URL,
                $params
            ),
            [
                'data-no-minify'=> '1',
            ]
        );
    }
}
