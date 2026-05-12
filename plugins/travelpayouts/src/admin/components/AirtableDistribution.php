<?php

namespace Travelpayouts\admin\components;

use Travelpayouts\components\AirtableApiModel;
use Travelpayouts\helpers\StringHelper;

class AirtableDistribution extends AirtableApiModel
{
    protected $data = [];

    protected $fieldKey = 'wordpess';

    public function shouldAddScript(): bool
    {
        if (empty($this->token)) {
            return false;
        }

        $atData = $this->getResponse();

        if (isset($atData['records'][0]['fields'][$this->fieldKey])) {
            return StringHelper::toBoolean($atData['records'][0]['fields'][$this->fieldKey]);
        }

        return false;
    }
}
