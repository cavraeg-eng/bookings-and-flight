<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\brands;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\api\ApiResponseObject;

class PlatformResponse extends ApiResponseObject
{
    /**
     * @var \Travelpayouts\components\brands\PlatformResponseSource[]
     */
    public $sources = [];

    /**
     * @var string|null
     */
    public $script_link;

    /**
     * @var string|null
     */
    public $script_domain;
    /**
     * @var string|null
     */
    public $widget_domain;
    /**
     * @var string|null
     */
    public $short_link_domain;

    /**
     * @Inject
     * @var \Travelpayouts\modules\account\AccountForm
     */
    protected $account;

    public function getSourceById($id): ?PlatformResponseSource
    {
        if (is_string($id) || is_int($id)) {
            foreach ($this->sources as $source) {
                if ($source->id === (int)$id) {
                    return $source;
                }
            }

        }

        return null;
    }

    public function getCurrentSource(): ?PlatformResponseSource
    {
        return $this->getSourceById($this->account->platform);
    }

    /**
     * Получаем список активных программ
     * @return int[]
     */
    public function getActivePrograms(): array
    {
        $source = $this->getCurrentSource();
        return $source ? $source->getAcceptedIds() : [];
    }

}

class PlatformResponseSource extends ApiResponseObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Travelpayouts\components\brands\ProgramIDS
     */
    public $program_ids;

    /**
     * @var \Travelpayouts\components\brands\Channel[]
     */
    public $channels;

    /**
     * @return int[]
     */
    public function getAcceptedIds(): array
    {
        return $this->program_ids && is_array($this->program_ids->accepted)
            ? $this->program_ids->accepted
            : [];
    }

}

class ProgramIDS extends ApiResponseObject
{
    /**
     * @var int[]
     */
    public $accepted;
}

class Channel extends ApiResponseObject
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string[]
     */
    public $links;
}
