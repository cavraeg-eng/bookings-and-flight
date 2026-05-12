<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts\v1\pricesCheap;

use Travelpayouts\modules\tables\components\api\travelpayouts\BaseTravelpayoutsApiModel;

/**
 * @method PricesCheapApiResponse[] getResponseModels()
 */
class PricesCheapApiModel extends BaseTravelpayoutsApiModel
{
    /**
     * @var string
     * Currency of prices
     */
    public $currency = 'RUB';
    public $origin;
    public $destination;
    /**
     * @var string
     *    Day or month of departure (yyyy-mm-dd or yyyy-mm).
     */
    public $depart_date;
    /**
     * @var string
     * Day or month of return (yyyy-mm-dd or yyyy-mm).
     */
    public $return_date;
    /**
     * @var int|string
     * Optional parameter, is used to display the found data (by default the page displays 100 found prices. If the
     *     destination isn't selected, there can be more data. In this case, use the page, to display the next set of
     *     data, for example, page=2).
     */
    public $page = 1;

    protected $responseClass = PricesCheapApiResponse::class;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['currency', 'origin', 'destination'], 'required'],
            [['currency', 'origin', 'destination'], 'string', 'length' => 3],
        ]);
    }

    public function afterRequest()
    {
        parent::afterRequest();
        $response = $this->response;
        if (is_array($response) && !empty($response)) {
            $response = array_shift($response);
            $responseMutated = $this->addNumberOfChanges($response);
            $this->response = $responseMutated;
        }
    }

    /**
     * Добавляет количество пересадок, так в ответе нет информации о пересадках
     * @param array $data
     * @return array
     */
    private function addNumberOfChanges($data)
    {
        $result = [];
        $by_airline = [];

        $first = true;
        foreach ($data as $key => $datum) {
            $by_airline[$datum['airline']][] = $datum;
            $count = 0;
            if (!$first) {
                $count = count($by_airline[$datum['airline']]);
            }

            $datum['number_of_changes'] = $count;
            $result[] = $datum;
            $first = false;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function endpointUrl()
    {
        return 'http://api.travelpayouts.com/v1/prices/cheap';
    }

}
