<?php

namespace Travelpayouts\modules\moneyScript;

use Travelpayouts;
use Travelpayouts\admin\redux\base\ModuleSection;
use Travelpayouts\components\httpClient\CachedClient;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\account\AccountForm;
use Travelpayouts\modules\moneyScript\components\SubscribedCampaign;
use Travelpayouts\modules\moneyScript\widgets\MoneyScriptSectionDescriptionWidget;

class MoneyScriptSection extends ModuleSection
{
    /**
     * @var string
     */
    protected $active;
    /**
     * @var array<string,string>
     */
    protected $exclude = [];
    /**
     * @Inject
     * @var AccountForm
     */
    protected $accountSection;

    /**
     * @var array
     */
    protected $_excludedCampaignIds;

    public function section(): array
    {
        return [
            'title' => Travelpayouts::__('Money Script'),
            'icon' => 'tp-i-tabler:coin',
            'desc' => MoneyScriptSectionDescriptionWidget::widget(),
        ];
    }

    public function fields(): array
    {
        $options = $this->getOptions();

        return [
            'active' => $this->fieldSwitcher()
                ->setTitle(Travelpayouts::__('Enable Money Script'))
                ->setDefault(false),
            count($options) ? [
                'id' => 'exclude',
                'type' => 'checkbox',
                'multi' => true,
                'title' => Travelpayouts::__('Exclude programs'),
                'options' => $options,
                'placeholder' => '',
                'select2' => [
                    'theme' => 'travelpayouts',
                    'allowClear' => false,
                    'minimumResultsForSearch' => 10,
                ],
            ] : null,
        ];
    }

    public function optionPath(): string
    {
        return 'money_script';
    }

    protected function getOptions(): array
    {
        $marker = $this->accountSection->api_marker;
        if ($marker) {

            $client = new CachedClient([], 60 * 60 * 3);
            $response = $client->get("https://app.travelpayouts.com/money_script_api/get_subscribed_campaings?marker=$marker");
            if (!$response->isError && $data = $response->json) {
                return $this->mapSubscribedCampaigns($data);
            }
        }
        return [];
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return StringHelper::toBoolean($this->active);
    }

    /**
     * Получаем id кампаний для исключения
     * @return string[]
     */
    public function getExcludedCampaignIds(): array
    {
        if (!$this->_excludedCampaignIds && $this->exclude && is_array($this->exclude)) {
            $activeCompanyList = array_filter($this->exclude, static function ($isActive) {
                if (is_string($isActive)) {
                    return $isActive === '1';
                }
                return false;
            });

            $this->_excludedCampaignIds = array_keys($activeCompanyList);
        }

        return $this->_excludedCampaignIds;
    }

    /**
     * @param $data
     * @return array|string[]
     */
    protected function mapSubscribedCampaigns($data): array
    {
        $campaigns = [];
        foreach ($data as $item) {
            $model = new SubscribedCampaign($item);
            if (isset($campaigns[$model->campaign_id])) {
                $campaigns[$model->campaign_id] = array_merge($campaigns[$model->campaign_id], $model->campaign_domains);
            } else {
                $campaigns[$model->campaign_id] = $model->campaign_domains;
            }
        }

        $result = [];
        foreach ($campaigns as $campaignId => $domainsList) {
            // Убираем неуникальные домены
            $domainsList = array_unique($domainsList);
            $campaignIdTitle = Travelpayouts::_x('Campaign id: {id}', 'moneyscript.excludedCampaigns', ['id' => $campaignId]) . '. ';
            $domainsListTitle = Travelpayouts::_x('Excluded domains', 'moneyscript.excludedCampaigns') . ': ' . implode(', ', $domainsList);
            $result[$campaignId] = $campaignIdTitle . $domainsListTitle;

        }
        return ksort($result) ? $result : [];
    }

}
