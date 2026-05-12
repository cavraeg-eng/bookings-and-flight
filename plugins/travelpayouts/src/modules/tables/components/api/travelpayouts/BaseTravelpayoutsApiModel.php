<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api\travelpayouts;

use Travelpayouts\modules\tables\components\api\BaseTokenApiModel;

abstract class BaseTravelpayoutsApiModel extends BaseTokenApiModel
{
    protected function request()
    {
        return $this->fetchApi();
    }

    /**
     * @inheritdoc
     */
    public function afterRequest()
    {
        $response = $this->response;
        if (is_array($response)) {
            if (isset($response['success']) && $response['success']) {
                $this->response = $response['data'];
            } else {
                $this->fetchErrors();
                $this->response = null;
            }
        }
    }

    protected function fetchErrors()
    {
        $response = $this->response;
        if (isset($response['errors'])) {
            foreach ($response['errors'] as $errorAttribute => $errorMessage) {
                $this->add_error($errorAttribute, $errorMessage);
            }
        }
        if (isset($response['error'])) {
            $this->add_error('token', $response['error']);
            if (TRAVELPAYOUTS_DEBUG) {
                echo $response['error'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function getRequestQueryString(): string
    {
        return '?' . http_build_query($this->normalizeQueryParams($this->toArray()));
    }

    /**
     * @param $params
     * @return array
     */
    protected function normalizeQueryParams($params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $result[$key] = $value ? 'true' : 'false';
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

}
