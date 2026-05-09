<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\hotelLook\locationMap;

use DateTimeImmutable;
use Travelpayouts\modules\tables\components\api\hotelLook\BaseHotelLookApiModel;

/**
 * Class LocationApiModel
 * @property $city
 * @property string $check_in
 * @property string $check_out
 * @property string $language
 */
class LocationApiModel extends BaseHotelLookApiModel
{
    public const SCENARIO_WITH_DATES_REQUIRED = 'withDates';
    public const DATE_INPUT_FORMAT = 'd-m-Y';
    public const DATE_OUTPUT_FORMAT = 'Y-m-d';

    public $currency;
    /**
     * @var string|int
     * id of the city
     */
    public $id;
    /**
     * @var string|int
     * limitation of output results from 1 to 100, default - 10;
     */
    public $limit = 10;

    /**
     * @var string
     * type of hotels from request /tp/public/available_selections.json
     */
    public $type;

    /**
     * @var string
     */
    public $check_in;

    /**
     * @var string
     */
    public $check_out;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'limit', 'language', 'type', 'currency'], 'required'],
            [['currency'], 'string', 'length' => 3],
            [['city', 'check_in', 'check_out'], 'safe'],
            [['check_in', 'check_out'], 'required', 'on' => [self::SCENARIO_WITH_DATES_REQUIRED]],
        ]);
    }

    protected function request()
    {
        return $this->fetchApi();
    }

    public function afterRequest()
    {
        if (is_array($this->response) && isset($this->response[$this->type])) {
            // сливаем содержимое вида ['$this->type'=>[]] в ['items'=>[]]
            $this->response = [
                'items' => $this->response[$this->type],
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function endpointUrl()
    {
        return 'https://yasen.hotellook.com/tp/v1/widget_location_dump.json';
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'language',
        ]);
    }

    public function formatDate($value): ?string
    {
        if (is_string($value)) {
            $date = DateTimeImmutable::createFromFormat(self::DATE_INPUT_FORMAT, $value);
            return $date
                ? $date->format(self::DATE_OUTPUT_FORMAT)
                : null;
        }
        return null;
    }
}
