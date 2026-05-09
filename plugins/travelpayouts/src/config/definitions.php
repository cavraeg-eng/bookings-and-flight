<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

use Rollbar\Payload\Level;
use Travelpayouts\admin\redux\ReduxExtension;

return [
	'version' => TRAVELPAYOUTS_VERSION,
	'name' => 'travelpayouts',
	'snowplow.context' => static function () {
		return [
			'wp_version' => get_bloginfo('version'),
			'php_version' => PHP_VERSION,
			'plugin_version' => defined('TRAVELPAYOUTS_VERSION')
				? TRAVELPAYOUTS_VERSION
				: '-',
			'domain' => home_url(),
			'plugin_locale' => get_user_locale(),
			'marker' => Travelpayouts::getInstance()->account->marker,
		];
	},
	'redux.config'=>  [
		'opt_name' => TRAVELPAYOUTS_REDUX_OPTION,
		'dev_mode' => false,
		'display_name' => 'Travelpayouts',
		'display_version' => TRAVELPAYOUTS_VERSION,
		'page_slug' => 'travelpayouts_options',
		'page_title' => 'Travelpayouts Options',
		'update_notice' => true,
		'intro_text' => null,
		'footer_text' => null,
		'admin_bar' => true,
		'menu_icon' => Travelpayouts::getAlias('@webImages/admin/panel/logo-short-gray.svg'),
		'menu_type' => 'menu',
		'menu_title' => 'Travelpayouts',
		'allow_sub_menu' => false,
		'page_parent_post_type' => 'travelpayouts_post_type',
		'customizer' => true,
		'default_mark' => '*',
		'hide_reset' => !TRAVELPAYOUTS_DEBUG || !TRAVELPAYOUTS_SETTINGS_RESET_BUTTON,
		'hints' => [
			'icon_position' => 'right',
			'icon_size' => 'normal',
			'tip_style' => [
				'color' => 'light',
			],
			'tip_position' => [
				'my' => 'top left',
				'at' => 'bottom right',
			],
			'tip_effect' => [
				'show' => [
					'duration' => '500',
					'event' => 'mouseover',
				],
				'hide' => [
					'duration' => '500',
					'event' => 'mouseleave unfocus',
				],
			],
		],
		'output' => true,
		'output_tag' => true,
		'settings_api' => true,
		'cdn_check_time' => '1440',
		'compiler' => true,
		'page_permissions' => 'manage_options',
		'save_defaults' => true,
		'show_import_export' => true,
		'transient_time' => '3600',
		'network_sites' => true,
		'templates_path' => __DIR__ . '/../admin/redux/templates/panel',
		'hide_expand' => true,
		'allow_tracking' => false,
        'extensions'=> [
            ReduxExtension::class
        ]
	]
];
