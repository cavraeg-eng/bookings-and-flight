<?php
/**
 * Redux_Travelpayouts_Instances Functions
 *
 * @package     Redux_Travelpayouts_Framework
 * @subpackage  Core
 * @deprecated Maintained for backward compatibility with v3.
 */

/**
 * Retreive an instance of TravelpayoutsSettingsFramework
 *
 * @depreciated
 *
 * @param  string $opt_name the defined opt_name as passed in $args.
 *
 * @return object                TravelpayoutsSettingsFramework
 */
function get_Redux_Travelpayouts_instance( $opt_name ) {
	_deprecated_function( __FUNCTION__, '4.0', 'Redux_Travelpayouts::instance($opt_name)' );

	return Redux_Travelpayouts::instance( $opt_name );
}

/**
 * Retreive all instances of TravelpayoutsSettingsFramework
 * as an associative array.
 *
 * @depreciated
 * @return array        format ['opt_name' => $TravelpayoutsSettingsFramework]
 */
function get_all_Redux_Travelpayouts_instances() {
	_deprecated_function( __FUNCTION__, '4.0', 'Redux_Travelpayouts::all_instances()' );

	return Redux_Travelpayouts::all_instances();
}
