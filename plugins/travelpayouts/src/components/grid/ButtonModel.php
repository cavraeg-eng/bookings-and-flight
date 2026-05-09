<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\components\tables\enrichment\UrlHelper;
use Travelpayouts\modules\account\AccountForm;
use Travelpayouts\modules\settings\SettingsForm;

abstract class ButtonModel extends BaseInjectedObject
{
    /**
     * @var string
     */
    public $subid;

    /**
     * @var string
     */
    public $linkMarker = '';

    /**
     * @var string
     */
    protected $apiMarker;
    /**
     * @var string
     */
    protected $platform;

    /**
     * @Inject
     * @var SettingsForm
     */
    protected $globalSettings;

    /**
     * @Inject
     * @var AccountForm
     */
    protected $accountSettings;

    public function init()
    {
        $this->platform = $this->accountSettings->platform;
        $this->apiMarker = $this->accountSettings->api_marker;
    }

    protected function getMarker(): ?string
    {
        return $this->apiMarker
            ? UrlHelper::get_marker($this->apiMarker, $this->subid, $this->linkMarker)
            : null;
    }

    abstract public function getUrl(): string;

}
