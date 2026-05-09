<?php

namespace Travelpayouts\components\rest\models;

use Travelpayouts;
use Travelpayouts\components\Model;

/**
 * Class GutenbergModulesModel
 * @package Travelpayouts\components\rest
 */
class GutenbergModulesModel extends Model
{
    private $modulesClassList = [
        'tables' => Travelpayouts\modules\tables\components\GutenbergRestModule::class,
        'widgets' => Travelpayouts\modules\searchForms\SearchFormRestModule::class,
        'links' => Travelpayouts\modules\links\components\GutenbergRestModule::class,
    ];

    /**
     * @var BaseGutenbergRestModule[]
     */
    private $modulesInstanceList = [];

    /**
     * @return BaseGutenbergRestModule[]
     */
    public function getRestModules()
    {
        if (empty($this->modulesInstanceList)) {
            $this->modulesInstanceList = array_map(static function ($moduleClassName) {
                return new $moduleClassName;
            }, $this->modulesClassList);
        }
        return $this->modulesInstanceList;
    }

    /**
     * Дополнительные переводы
     * @return array
     */
    private function translations()
    {
        return [
            'button_title_save' => Travelpayouts::__('Save'),
            'button_title_cancel' => Travelpayouts::__('Cancel'),
            'select_title' => Travelpayouts::__('Select a program'),
            'button_title_setting' => Travelpayouts::__('Settings'),
            'shortcode_insert_failure' => Travelpayouts::__('An error occured while pasting the shortcode'),
            'you_can_use_the_following_variables_in_this_field' => Travelpayouts::__('You can use the following variables in this field:'),
        ];
    }

    public function fields()
    {
        return [
            'modules' => 'restModules',
            'extraData' => function () {
                return [
                    'translations' => $this->translations(),
                ];
            },
        ];
    }
}
