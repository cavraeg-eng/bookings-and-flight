<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\api;

use Travelpayouts;
use Travelpayouts\components\ApiModel;

/**
 * Class BaseTokenApiModel
 * @package Travelpayouts\modules\tables\components\api
 */
abstract class BaseTokenApiModel extends ApiModel
{
    /**
     * @var string
     * Individual affiliate token.
     */
    public $token;

    public function __construct($config = [])
    {
        if ($accountModule = Travelpayouts::getInstance()->account) {
            $this->token = $accountModule->token;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['token'], 'required'],
            ['token', 'validateApiToken'],
        ];
    }

    public function validateApiToken($attribute)
    {
        $length = strlen($this->$attribute);

        if (!($length === 32 || $length === 40)) {
            $this->add_error($attribute, Travelpayouts::__('Your Travelpayouts API token should contain {first_number} or {second_number} characters. Please check that it is entered correctly.', [
                'first_number' => 32,
                'second_number' => 40,
            ]));
        }
    }
}
