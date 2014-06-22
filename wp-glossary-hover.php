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
 * Version:           1.1.0
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

/*
 * Load public WP Glossary Hover classes.
 */
require_once(plugin_dir_path(__FILE__) . 'public/includes/class-tooltip-parser.php');

/*
 * WP Glossary Hover class. This class works with the public-facing side of the
 * WordPress site.
 */
require_once(plugin_dir_path(__FILE__) . 'public/class-wp-glossary-hover.php');

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

	require_once(plugin_dir_path(__FILE__) . 'admin/includes/enums/class-base-enum.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/enums/class-field-types-enum.php');

	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-base-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-checkbox-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-integer-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-select-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-textbox-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-multi-checkbox-field.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/fields/class-color-picker-field.php');

	require_once(plugin_dir_path(__FILE__) . 'admin/includes/settings/class-settings-section.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/settings/class-settings-tab.php');
	require_once(plugin_dir_path(__FILE__) . 'admin/includes/settings/class-plugin-settings.php');

	require_once(plugin_dir_path(__FILE__) . 'admin/class-wp-glossary-hover-admin.php');
	add_action('plugins_loaded', array('WP_Glossary_Hover_Admin', 'get_instance'));

}
