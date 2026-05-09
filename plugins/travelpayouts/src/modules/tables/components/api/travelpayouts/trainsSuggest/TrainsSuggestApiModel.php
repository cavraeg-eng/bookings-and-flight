<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\trainsSuggest;

use Travelpayouts\components\ApiModel;

/**
 * Class TrainsSuggestApiModel
 * @package Travelpayouts\modules\tables\components\api\travelpayouts
 * @property int|string $origin
 * @property int|string $destination
 */
class TrainsSuggestApiModel extends ApiModel
{
    public $service = 'tutu_trains';
    /**
     * @var string|int
     * @see setOrigin()
     * @see getOrigin()
     */
    public $term;
    /**
     * @var string|int
     * @see getDestination()
     * @see setDestination()
     */
    public $term2;

    protected $cacheTime = 60 * 60 * 12;

    protected function request()
    {
        return $this->fetchApi();
    }

    public function rules()
    {
        return [
            [['origin', 'destination'], 'number'],
            [['term', 'term2'], 'required'],
        ];
    }

    public function attribute_labels()
    {
        return [
            'term' => 'origin',
            'term2' => 'destination',
        ];
    }

    public function setOrigin($value)
    {
        $this->term = $value;
    }

    public function getOrigin()
    {
        return $this->term;
    }

    public function setDestination($value)
    {
        $this->term2 = $value;
    }

    public function getDestination()
    {
        return $this->term2;
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'https://suggest.travelpayouts.com/search';
    }
}
