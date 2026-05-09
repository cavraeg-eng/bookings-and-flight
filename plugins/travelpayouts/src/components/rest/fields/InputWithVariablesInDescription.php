<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\fields;

use Travelpayouts\helpers\ArrayHelper;

class InputWithVariablesInDescription extends Input
{
    public $type = 'input-with-variables-in-description';

    /**
     * @var Array<string,string>
     */
    public $variables = [];

    public function setVariables(array $values): self
    {
        if (ArrayHelper::isAssociative($values)) {
            $this->variables = $values;
        }
        return $this;
    }
}