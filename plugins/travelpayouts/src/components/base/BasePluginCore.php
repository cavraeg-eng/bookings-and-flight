<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\base;

use Exception;
use Travelpayouts\components\BaseInjectedObject;
use Travelpayouts\traits\SingletonTrait;

/**
 * Class BasePluginCore
 * @package Travelpayouts\components\base
 */
abstract class BasePluginCore extends BaseInjectedObject
{
	use SingletonTrait;

	/**
	 * @var string[]
	 */
	public static $aliases = [];

	public function __construct($config = [])
	{
		self::$_instances[static::class] = $this;
		$this->registerAliases();
		parent::__construct($config);
	}

	/**
	 * @return string[]
	 */
	abstract protected function aliasList();

	protected function registerAliases()
	{
		$aliasList = $this->aliasList();
		foreach ($aliasList as $alias => $path) {
			self::setAlias($alias, $path);
		}
	}

	/**
	 * Translates a path alias into an actual path.
	 * The translation is done according to the following procedure:
	 * 1. If the given alias does not start with '@', it is returned back without change;
	 * 2. Otherwise, look for the longest registered alias that matches the beginning part
	 *    of the given alias. If it exists, replace the matching part of the given alias with
	 *    the corresponding registered path.
	 * 3. Throw an exception or return false, depending on the `$throwException` parameter.
	 * @param string $alias the alias to be translated.
	 * @param bool $throwException whether to throw an exception if the given alias is invalid.
	 * If this is false and an invalid alias is given, false will be returned by this method.
	 * @return string|bool the path corresponding to the alias, false if the root alias is not previously registered.
	 * @throws Exception if the alias is invalid while $throwException is true.
	 * @see setAlias()
	 */
	public static function getAlias($alias, $throwException = true)
	{
		if (strncmp($alias, '@', 1)) {
			// not an alias
			return $alias;
		}
		$pos = strpos($alias, '/');
		$root = $pos === false ? $alias : substr($alias, 0, $pos);
		if (isset(static::$aliases[$root])) {
			if (is_string(static::$aliases[$root])) {
				return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
			}
			foreach (static::$aliases[$root] as $name => $path) {
				if (strpos($alias . '/', $name . '/') === 0) {
					return $path . substr($alias, strlen($name));
				}
			}
		}
		if ($throwException) {
			throw new Exception("Invalid path alias: $alias");
		}
		return false;
	}

	/**
	 * Registers a path alias.
	 * A path alias is a short name representing a long path (a file path, a URL, etc.)
	 * For example, we use '@yii' as the alias of the path to the Yii framework directory.
	 * A path alias must start with the character '@' so that it can be easily differentiated
	 * from non-alias paths.
	 * Note that this method does not check if the given path exists or not. All it does is
	 * to associate the alias with the path.
	 * Any trailing '/' and '\' characters in the given path will be trimmed.
	 * See the [guide article on aliases](guide:concept-aliases) for more information.
	 * @param string $alias the alias name (e.g. "@yii"). It must start with a '@' character.
	 * It may contain the forward slash '/' which serves as boundary character when performing
	 * alias translation by [[getAlias()]].
	 * @param string $path the path corresponding to the alias. If this is null, the alias will
	 * be removed. Trailing '/' and '\' characters will be trimmed. This can be
	 * - a directory or a file path (e.g. `/tmp`, `/tmp/main.txt`)
	 * - a URL (e.g. `http://www.yiiframework.com`)
	 * - a path alias (e.g. `@yii/base`). In this case, the path alias will be converted into the
	 *   actual path first by calling [[getAlias()]].
	 * @throws Exception if $path is an invalid alias.
	 * @see getAlias()
	 */
	public static function setAlias($alias, $path)
	{
		if (strncmp($alias, '@', 1)) {
			$alias = '@' . $alias;
		}
		$pos = strpos($alias, '/');
		$root = $pos === false ? $alias : substr($alias, 0, $pos);
		if ($path !== null) {
			$path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
			if (!isset(static::$aliases[$root])) {
				if ($pos === false) {
					static::$aliases[$root] = $path;
				} else {
					static::$aliases[$root] = [$alias => $path];
				}
			} elseif (is_string(static::$aliases[$root])) {
				if ($pos === false) {
					static::$aliases[$root] = $path;
				} else {
					static::$aliases[$root] = [
						$alias => $path,
						$root => static::$aliases[$root],
					];
				}
			} else {
				static::$aliases[$root][$alias] = $path;
				krsort(static::$aliases[$root]);
			}
		} elseif (isset(static::$aliases[$root])) {
			if (is_array(static::$aliases[$root])) {
				unset(static::$aliases[$root][$alias]);
			} elseif ($pos === false) {
				unset(static::$aliases[$root]);
			}
		}
	}

	/**
	 * Retrieve the translation of $text.
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 * You can add parameters to a translation message that will be substituted with the corresponding value after
	 * translation. The format for this is to use curly brackets around the parameter name as you can see in the
	 * following example:
	 * ```php
	 * $username = 'Andrey';
	 * echo Travelpayouts::__('app', 'Hello, {username}!', ['username' => $username]);
	 * ```
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @return string Translated text.
	 * @see __()
	 */
	public static function __($text, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params($text, $params);
		}

		$domain = self::get_text_domain();
		$message = __($text, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Retrieve the translation of $text and escapes it for safe use in an attribute.
	 * If there is no translation, or the text domain isn't loaded, the original text is returned.
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @return string Translated text on success, original text on failure.
	 * @see esc_attr__()
	 */
	public static function esc_attr__($text, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params(esc_attr($text), $params);
		}

		$domain = self::get_text_domain();
		$message = esc_attr__($text, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Retrieve the translation of $text and escapes it for safe use in HTML output.
	 * If there is no translation, or the text domain isn't loaded, the original text
	 * is escaped and returned.
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @return string Translated text
	 * @see esc_html__()
	 */
	public static function esc_html__($text, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params(esc_html($text), $params);
		}

		$domain = self::get_text_domain();

		$message = esc_html__($text, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Display translated text.
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @see _e()
	 */
	public static function _e($text, $params = [])
	{
		if (!did_action('init')) {
			echo self::parse_message_params($text, $params);
			return;
		}

		$domain = self::get_text_domain();
		$message = translate($text, $domain);
		echo self::parse_message_params($message, $params);
	}

	/**
	 * Display translated text that has been escaped for safe use in an attribute.
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @see esc_attr_e()
	 */
	public static function esc_attr_e($text, $params = [])
	{
		if (!did_action('init')) {
			echo self::parse_message_params(esc_attr($text), $params);
			return;
		}

		$domain = self::get_text_domain();
		$message = esc_attr(translate($text, $domain));
		echo self::parse_message_params($message, $params);
	}

	/**
	 * Display translated text that has been escaped for safe use in HTML output.
	 * @param string $text Text to translate.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @see esc_html_e()
	 */
	public static function esc_html_e($text, $params = [])
	{
		if (!did_action('init')) {
			echo self::parse_message_params(esc_html($text), $params);
			return;
		}

		$domain = self::get_text_domain();

		$message = esc_html(translate($text, $domain));
		echo self::parse_message_params($message, $params);
	}

	/**
	 * Retrieve translated string with gettext context.
	 * Quite a few times, there will be collisions with similar translatable text
	 * found in more than two places, but with different translated context.
	 * By including the context in the pot file, translators can translate the two
	 * strings differently.
	 * @param string $text Text to translate.
	 * @param string $context Context information for the translators.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 *                        Default 'default'.
	 * @return string Translated context string without pipe.
	 * @see _x()
	 */
	public static function _x($text, $context, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params($text, $params);
		}

		$domain = self::get_text_domain();
		$message = _x($text, $context, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Display translated string with gettext context.
	 * @param string $text Text to translate.
	 * @param string $context Context information for the translators.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 *                        Default 'default'.
	 * @return string Translated context string without pipe.
	 * @see _ex()
	 */
	public static function _ex($text, $context, $params = [])
	{
		if (!did_action('init')) {
			echo self::parse_message_params($text, $params);
			return;
		}

		$domain = self::get_text_domain();

		$message = _x($text, $context, $domain);
		echo self::parse_message_params($message, $params);
	}

	/**
	 * Translate string with gettext context, and escapes it for safe use in an attribute.
	 * @param string $text Text to translate.
	 * @param string $context Context information for the translators.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 *                        Default 'default'.
	 * @return string Translated text
	 * @see esc_attr_x()
	 */
	public static function esc_attr_x($text, $context, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params(esc_attr($text), $params);
		}

		$domain = self::get_text_domain();
		$message = esc_attr_x($text, $context, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Translate string with gettext context, and escapes it for safe use in HTML output.
	 * @param string $text Text to translate.
	 * @param string $context Context information for the translators.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 *                        Default 'default'.
	 * @return string Translated text.
	 * @see esc_html_x()
	 */
	public static function esc_html_x($text, $context, $params = [])
	{
		if (!did_action('init')) {
			return self::parse_message_params(esc_html($text), $params);
		}

		$domain = self::get_text_domain();

		$message = esc_html_x($text, $context, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Translates and retrieves the singular or plural form based on the supplied number.
	 * Used when you want to use the appropriate form of a string based on whether a
	 * number is singular or plural.
	 * Example:
	 *     printf( _n( '%s person', '%s people', $count, 'text-domain' ), number_format_i18n( $count ) );
	 * @param string $single The text to be used if the number is singular.
	 * @param string $plural The text to be used if the number is plural.
	 * @param int $number The number to compare against to use either the singular or plural form.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 * @return string The translated singular or plural form.
	 * @see _n()
	 */
	public static function _n($single, $plural, $number, $params = [])
	{
		if (!did_action('init')) {
			$message = ($number == 1) ? $single : $plural;
			return self::parse_message_params($message, $params);
		}

		$domain = self::get_text_domain();
		$message = _n($single, $plural, $number, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
	 * This is a hybrid of _n() and _x(). It supports context and plurals.
	 * Used when you want to use the appropriate form of a string with context based on whether a
	 * number is singular or plural.
	 * Example of a generic phrase which is disambiguated via the context parameter:
	 *     printf( _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n(
	 * $people ) ); printf( _nx( '%s group', '%s groups', $animals, 'group of animals', 'text-domain' ),
	 * number_format_i18n( $animals ) );
	 * @param string $single The text to be used if the number is singular.
	 * @param string $plural The text to be used if the number is plural.
	 * @param int $number The number to compare against to use either the singular or plural form.
	 * @param string $context Context information for the translators.
	 * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
	 *                        Default 'default'.
	 * @return string The translated singular or plural form.
	 * @see _nx()
	 */
	public static function _nx($single, $plural, $number, $context, $params = [])
	{
		if (!did_action('init')) {
			$message = ($number == 1) ? $single : $plural;
			return self::parse_message_params($message, $params);
		}

		$domain = self::get_text_domain();
		$message = _nx($single, $plural, $number, $context, $domain);
		return self::parse_message_params($message, $params);
	}

	/**
	 * Registers plural strings in POT file, but does not translate them.
	 * Used when you want to keep structures with translatable plural
	 * strings and use them later when the number is known.
	 * Example:
	 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
	 *     ...
	 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
	 * @param string $singular Singular form to be localized.
	 * @param string $plural Plural form to be localized.
	 * @return array {
	 *     Array of translation information for the strings.
	 * @type string $0        Singular form to be localized. No longer used.
	 * @type string $1        Plural form to be localized. No longer used.
	 * @type string $singular Singular form to be localized.
	 * @type string $plural Plural form to be localized.
	 * @type null $context Context information for the translators.
	 * @type string $domain Text domain.
	 * }
	 * @see _n_noop()
	 */
	public static function _n_noop($singular, $plural)
	{
		if (!did_action('init')) {
			return [
				0 => $singular,
				1 => $plural,
				'singular' => $singular,
				'plural' => $plural,
				'context' => null,
				'domain' => self::get_text_domain(),
			];
		}

		return _n_noop($singular, $plural, self::get_text_domain());
	}

	/**
	 * Registers plural strings with gettext context in POT file, but does not translate them.
	 * Used when you want to keep structures with translatable plural
	 * strings and use them later when the number is known.
	 * Example of a generic phrase which is disambiguated via the context parameter:
	 *     $messages = array(
	 *          'people'  => _nx_noop( '%s group', '%s groups', 'people', 'text-domain' ),
	 *          'animals' => _nx_noop( '%s group', '%s groups', 'animals', 'text-domain' ),
	 *     );
	 *     ...
	 *     $message = $messages[ $type ];
	 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
	 * @param string $singular Singular form to be localized.
	 * @param string $plural Plural form to be localized.
	 * @param string $context Context information for the translators.
	 * @return array {
	 *     Array of translation information for the strings.
	 * @type string $0        Singular form to be localized. No longer used.
	 * @type string $1        Plural form to be localized. No longer used.
	 * @type string $2        Context information for the translators. No longer used.
	 * @type string $singular Singular form to be localized.
	 * @type string $plural Plural form to be localized.
	 * @type string $context Context information for the translators.
	 * @type string $domain Text domain.
	 * }
	 * @see _nx_noop()
	 */
	public static function _nx_noop($singular, $plural, $context)
	{
		if (!did_action('init')) {
			return [
				0 => $singular,
				1 => $plural,
				'singular' => $singular,
				'plural' => $plural,
				'context' => $context,
				'domain' => self::get_text_domain(),
			];
		}

		return _nx_noop($singular, $plural, $context, self::get_text_domain());
	}

	protected static function get_text_domain()
	{
		return TRAVELPAYOUTS_TEXT_DOMAIN;
	}

	protected static function parse_message_params($message, $params = [])
	{
		$placeholders = [];
		foreach ((array)$params as $name => $value) {
			$placeholders['{' . $name . '}'] = $value;
		}

		return ($placeholders === []) ? $message : strtr($message, $placeholders);
	}
}
