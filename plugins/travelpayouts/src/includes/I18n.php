<?php

/**
 * Define the internationalization functionality
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 */

namespace Travelpayouts\includes;

use Travelpayouts\components\BaseObject;

/**
 * Define the internationalization functionality.
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 */
class I18n extends BaseObject
{
	/**
	 * @var string
	 */
	protected $domain;
	/**
	 * @var string
	 */
	protected $localePath;

	public function register()
	{
		$this->loadTextDomain($this->domain, $this->localePath);
	}

	protected function loadTextDomain($domain, $localeSourcePath)
	{
		$locale = $this->getLocale($domain);
		$moFileName = $domain . '-' . $locale . '.mo';
		$moFilePath = $localeSourcePath . '/' . $moFileName;

		return is_readable($moFilePath)
			? load_textdomain($domain, $moFilePath)
			: false;
	}

	protected function getLocale($domain)
	{
		return apply_filters('plugin_locale', get_locale(), $domain);
	}
}
