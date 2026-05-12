<?php
/**
 * Redux_Travelpayouts Extension Abstract
 *
 * @class   Redux_Travelpayouts_Extension_Abstract
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Redux_Travelpayouts_Extension_Abstract
 * An abstract class to make the writing of redux extensions easier by allowing users to extend this class
 *
 * @see the samples directory to find an usage example
 */
abstract class Redux_Travelpayouts_Extension_Abstract {
	/**
	 * The version of the extension (This is a default value you may want to override it)
	 *
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * The extension URL.
	 *
	 * @var string
	 */
	protected $extension_url;

	/**
	 * The extension dir.
	 *
	 * @var string
	 */
	protected $extension_dir;

	/**
	 * The instance of the extension
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * The extension's file
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * The redux framework instance that spawned the extension.
	 *
	 * @var TravelpayoutsSettingsFramework
	 */
	public $parent;

	/**
	 * The ReflectionClass of the extension
	 *
	 * @var ReflectionClass
	 */
	protected $reflection_class;

	/**
	 * Redux_Travelpayouts_Extension_Abstract constructor.
	 *
	 * @param object $parent TravelpayoutsSettingsFramework pointer.
	 * @param string $file   Extension file.
	 */
	public function __construct( $parent, $file = '' ) {
		$this->parent = $parent;

		// If the file is not given make sure we have one.
		if ( empty( $file ) ) {
			$file = $this->get_reflection()->getFileName();
		}

		$this->file = $file;

		$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( $file ) ) );

		$plugin_info = Redux_Travelpayouts_Functions_Ex::is_inside_plugin( $this->file );

		if ( false !== $plugin_info ) {
			$this->extension_url = trailingslashit( dirname( $plugin_info['url'] ) );
		} else {
			$theme_info = Redux_Travelpayouts_Functions_Ex::is_inside_theme( $this->file );
			if ( false !== $theme_info ) {
				$this->extension_url = trailingslashit( dirname( $theme_info['url'] ) );
			}
		}
        $this->init();
		static::$instance = $this;
	}

    /**
     * Init function.
     * @return void
     */
    public function init(): void
    {

    }

	/**
	 * Get the reflection class of the extension.
	 *
	 * @return ReflectionClass
	 */
	protected function get_reflection() {
		if ( ! isset( $this->reflection_class ) ) {
			try {
				$this->reflection_class = new ReflectionClass( $this );
			} catch ( ReflectionException $e ) { // phpcs:ignore
				error_log( $e->getMessage() ); // phpcs:ignore
			}
		}

		return $this->reflection_class;
	}

	/**
	 * Return extension version.
	 *
	 * @return string
	 */
	public static function get_version() {
		return static::$version;
	}

	/**
	 * Returns extension instance.
	 *
	 * @return Redux_Travelpayouts_Extension_Abstract
	 */
	public static function get_instance() {
		return static::$instance;
	}

	/**
	 * Return extension dir.
	 *
	 * @return string
	 */
	public function get_dir() {
		return $this->extension_dir;
	}

	/**
	 * Returns extension URL
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->extension_url;
	}

	/**
	 * Adds the local field. (The use of add_field is recommended).
	 *
	 * @param string $field_name Name of field.
	 */
	protected function add_overload_field_filter( $field_name ) {
		// phpcs:ignore WordPress.NamingConventions.ValidHookName
		add_filter(
			'redux_travelpayouts/' . $this->parent->args['opt_name'] . '/field/class/' . $field_name,
			array(
				&$this,
				'overload_field_path',
			),
			10,
			2
		);
	}

	/**
	 * Adds the local field to the extension and register it in the builder.
	 *
	 * @param string $field_name Name of field.
	 */
	protected function add_field( $field_name ) {
		$class = $this->get_reflection()->getName();

		// phpcs:ignore WordPress.NamingConventions.ValidHookName
		add_filter(
			'redux_travelpayouts/fields',
			function ( $classes ) use ( $field_name, $class ) {
				$classes[ $field_name ] = $class;
				return $classes;
			}
		);

		$this->add_overload_field_filter( $field_name );
	}

	/**
	 * Overload field path.
	 *
	 * @param string $file  Extension file.
	 * @param array  $field Field array.
	 *
	 * @return string
	 */
	public function overload_field_path( $file, $field ) {
		$filename_fix = str_replace( '_', '-', $field['type'] );

		$files = array(
			trailingslashit( dirname( $this->file ) ) . $field['type'] . DIRECTORY_SEPARATOR . 'field_' . $field['type'] . '.php',
			trailingslashit( dirname( $this->file ) ) . $field['type'] . DIRECTORY_SEPARATOR . 'class-travelpayouts-' . $filename_fix . '.php',
		);

		$filename = Redux_Travelpayouts_Functions::file_exists_ex( $files );

		return $filename;
	}

	/**
	 * Sets the minimum version of Redux_Travelpayouts to use.  Displays a notice if requirments not met.
	 *
	 * @param string $min_version       Minimum version to evaluate.
	 * @param string $extension_version Extension version number.
	 * @param string $friendly_name     Friend extension name for notice display.
	 *
	 * @return bool
	 */
	public function is_minimum_version( $min_version = '', $extension_version = '', $friendly_name = '' ) {
		$Redux_Travelpayouts_ver = Redux_Travelpayouts_Core::$version;

		if ( '' !== $min_version ) {
			if ( version_compare( $Redux_Travelpayouts_ver, $min_version ) < 0 ) {
				// translators: %1$s Extension friendly name. %2$s: minimum Redux_Travelpayouts version.
				$msg = '<strong>' . sprintf( esc_html__( 'The %1$s extension requires Redux_Travelpayouts Framework version %2$s or higher.', 'redux-framework' ), $friendly_name, $min_version ) . '</strong>&nbsp;&nbsp;' . esc_html__( 'You are currently running Redux_Travelpayouts Framework version ', 'redux-framework' ) . ' ' . $Redux_Travelpayouts_ver . '.<br/><br/>' . esc_html__( 'This field will not render in your option panel, and featuress of this extension will not be available until the latest version of Redux_Travelpayouts Framework has been installed.', 'redux-framework' );

				$data = array(
					'parent'  => $this->parent,
					'type'    => 'error',
					'msg'     => $msg,
					'id'      => $this->ext_name . '_notice_' . $extension_version,
					'dismiss' => false,
				);

				if ( method_exists( 'Redux_Travelpayouts_Admin_Notices', 'set_notice' ) ) {
					Redux_Travelpayouts_Admin_Notices::set_notice( $data );
				} else {
					echo '<div class="error">';
					echo '<p>';
					echo $msg; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '</p>';
					echo '</div>';
				}

				return false;
			}
		}

		return true;
	}

    /**
     * @param class-string<Redux_Travelpayouts_Field> $fieldClass
     * @param string $fieldName
     * @return void
     * @throws Exception
     */
    protected function addPsr4Field(string $fieldClass, string $fieldName): void
    {
        if (!is_subclass_of($fieldClass, Redux_Travelpayouts_Field::class)) {
            throw new Exception('Field class must be subclass of BaseReduxExtensionField');
        }
        $fieldPath = 'redux_travelpayouts/' . $this->parent->args['opt_name'] . '/field';
        // overriding field file path
        add_filter($fieldPath . '/class/' . $fieldName, function () use ($fieldClass) {
            return Redux_Travelpayouts_Helpers::getFilePathFromClassName($fieldClass);
        });
        // overriding field class name
        add_filter($fieldPath . '/class-psr4/' . $fieldName, function () use ($fieldClass) {
            return $fieldClass;
        });
    }
}

if ( ! class_exists( 'Redux_Travelpayouts_Abstract_Extension' ) ) {
	class_alias( 'Redux_Travelpayouts_Extension_Abstract', 'Redux_Travelpayouts_Abstract_Extension' );
}
