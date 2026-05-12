<?php

namespace Travelpayouts\includes;

use Redux_Travelpayouts;
use Travelpayouts\components\DisabledRedux;

class ReduxConfigurator
{
    private $opt_name;
    private $_options;

    /**
     * Redux constructor.
     * @param $opt_name
     * @param array $args
     */
    public function __construct($opt_name, array $args)
    {
        /**
         * Даже если redux не активирован, передаем название опции в которой записаны настройки их можно использовать
         * Для тех кто еще не пользовался новой версии настройки в эту опцию будут записаны импортом
         */
        $this->opt_name = $opt_name;

        if (!class_exists('Redux_Travelpayouts')) return;

        Redux_Travelpayouts::setArgs($this->opt_name, $args);
    }

    /**
     * @param array $params
     */
    public function section(array $params)
    {
        Redux_Travelpayouts::setSection($this->opt_name, $params);
    }

    public function get_options_name()
    {
        return $this->opt_name;
    }

    public function get_options()
    {
        if (!$this->_options) {
            $option_name = $this->opt_name;
            $this->_options = get_option($option_name);
        }
        return $this->_options;
    }

    public function setOption($key, $value)
    {
        if (class_exists('Redux_Travelpayouts')) {
            $isSuccess = Redux_Travelpayouts::setOption($this->opt_name, $key, $value);
            /**
             * Зачищаем закешированное значение для повторного получения значений из getOptions
             * в случае успешного сохранения
             */
            if($isSuccess && ''!==$this->opt_name && ""!== $key){
                $optionName = $this->opt_name;
                global $$optionName;
                $$optionName = null;
            }
            return $isSuccess;
        }
        return DisabledRedux::setOption($this->opt_name, $key, $value);
    }

    public function getOption($name, $defaultValue = null)
    {
        if (class_exists('Redux_Travelpayouts')) {
            return Redux_Travelpayouts::getOption($this->opt_name, $name, $defaultValue);
        }
        return $defaultValue;
    }


    public function clearCache()
    {
        $this->_options = null;
    }
}
