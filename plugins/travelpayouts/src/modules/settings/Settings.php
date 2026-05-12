<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\settings;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\admin\redux\ReduxOptions;
use Travelpayouts\components\module\ModuleRedux;
use Travelpayouts\components\Translator;

/**
 * Class Settings
 * @package Travelpayouts\src\modules\settings
 * @property-read Dot $data
 * @property-read string $language
 * @property-read string $currency
 * @property-read bool $useRedirect
 */
class Settings extends ModuleRedux
{
    const DEFAULT_CURRENCY = 'USD';

    /**
     * @Inject
     * @var SettingsForm
     */
    public $section;

    /**
     * @return Dot
     */
    public function getData()
    {
        return $this->section->data;
    }


    public function registerSection()
    {
        $this->section->register();
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->data->get('language', Translator::DEFAULT_TRANSLATION);
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->data->get('currency', self::DEFAULT_CURRENCY);
    }

    /**
     * @return bool
     */
    public function getUseRedirect()
    {
        return (bool)$this->data->get('redirect', false);
    }

    public function getUseFileCache()
    {
        return (bool)$this->data->get('use_fileCache', false);
    }

    public function getScriptLocation()
    {
        return ReduxOptions::script_locations_values($this->data->get('script_location','in_footer'));
    }
}
