<?php
/**
 * WP Glossary Hover
 *
 * A WordPress Plugin that shows user created definitions for words or phrases
 * when hovering over said word or phrase.
 *
 * @package   WP Glossary Hover
 * @author    Chris Horton <chorton2227@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/chorton2227/WP-Glossary-Hover
 * @copyright 2014 Chris Horton
 *
 * @wordpress-plugin
 * Plugin Name:       WP Glossary Hover
 * Plugin URI:        https://github.com/chorton2227/WP-Glossary-Hover
 * Description:       When hovering over a word or phrase show the user created definition.
 * Version:           1.2.3
 * Author:            Chris Horton <chorton2227@gmail.com>
 * Author URI:        https://github.com/chorton2227
 * Text Domain:       wp-glossary-hover
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/chorton2227/WP-Glossary-Hover
 */

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

$require_files = array(
	'public/includes/class-tooltip-parser.php',
	'public/class-wp-glossary-hover.php',
	'includes/class-plugin-config.php',
	'includes/enums/class-base-enum.php',
	'includes/enums/class-field-types-enum.php',
	'includes/fields/class-base-field.php',
	'includes/fields/class-checkbox-field.php',
	'includes/fields/class-integer-field.php',
	'includes/fields/class-select-field.php',
	'includes/fields/class-textbox-field.php',
	'includes/fields/class-multi-checkbox-field.php',
	'includes/fields/class-color-picker-field.php',
	'includes/settings/class-settings-section.php',
	'includes/settings/class-settings-tab.php',
	'includes/settings/class-plugin-settings.php'
);

foreach ($require_files as $file)
{
	require_once(plugin_dir_path(__FILE__) . $file);
}

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, array('WP_Glossary_Hover', 'activate'));
register_deactivation_hook(__FILE__, array('WP_Glossary_Hover', 'deactivate'));

/*
 * Load instance of WP Glossary Hover class.
 */
add_action('plugins_loaded', array('WP_Glossary_Hover', 'get_instance'));

/*
 * Load instance of WP Glossary Hover Plugin Config class.
 */
add_action('plugins_loaded', array('WPGH_Plugin_Config', 'get_instance'));

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if (is_admin()) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if (is_admin() && ( ! defined('DOING_AJAX') || ! DOING_AJAX)) {

	$require_files = array(
		'admin/class-wp-glossary-hover-admin.php'
	);

	foreach ($require_files as $file)
	{
		require_once(plugin_dir_path(__FILE__) . $file);
	}

	add_action('plugins_loaded', array('WP_Glossary_Hover_Admin', 'get_instance'));

}

unset($file);
unset($require_files);