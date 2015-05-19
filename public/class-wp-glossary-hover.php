<?php
/**
 * WP Glossary Hover Plugin
 *
 * @package   WP_Glossary_Hover
 * @author    Chris Horton <chorton2227@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/chorton2227/WP-Glossary-Hover
 * @copyright 2014 Chris Horton
 */

/**
 * WP Glossary Hover class. This class works with the public-facing side of the WordPress site.
 *
 * @package	WP_Glossary_Hover
 * @author	Chris Horton <chorton2227@gmail.com>
 */
class WP_Glossary_Hover {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.2.2';

	/**
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	const PLUGIN_SLUG = 'wp-glossary-hover';

	/**
	 * Unique identifier for custom post type.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	const GLOSSARY_POST_TYPE = 'wpgh-glossary';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since    1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action('init', array($this, 'load_plugin_textdomain'));

		// Register custom post type
		add_action('init', array($this, 'wpgh_glossary_init'));

		// Activate plugin when new blog is added
		add_action('wpmu_new_blog', array($this, 'activate_new_site'));

		// Load public-facing style sheet and JavaScript.
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		// Add filter to the content
		$tooltip_parser = new WPGH_Tooltip_Parser(WP_Glossary_Hover::GLOSSARY_POST_TYPE);
		add_filter('the_content', array($tooltip_parser, 'parse_content'));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate($network_wide) {

		if (function_exists('is_multisite') && is_multisite()) {

			if ($network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ($blog_ids as $blog_id) {

					switch_to_blog($blog_id);
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {

		if (function_exists('is_multisite') && is_multisite()) {

			if ($network_wide) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ($blog_ids as $blog_id) {

					switch_to_blog($blog_id);
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site($blog_id) {

		if (1 !== did_action('wpmu_new_blog')) {
			return;
		}

		switch_to_blog($blog_id);
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col($sql);

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() { }

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() { }

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = WP_Glossary_Hover::PLUGIN_SLUG;
		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, FALSE, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');

	}

	/**
	* Register a glossary post type.
	*
	* @link http://codex.wordpress.org/Function_Reference/register_post_type
	*/
	public function wpgh_glossary_init() {

		$labels = array(
			'name'               => __('Glossary Terms', WP_Glossary_Hover::PLUGIN_SLUG),
			'singular_name'      => __('Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'menu_name'          => __('Glossary Terms', WP_Glossary_Hover::PLUGIN_SLUG),
			'name_admin_bar'     => __('Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'add_new'            => __('Add New', WP_Glossary_Hover::PLUGIN_SLUG),
			'add_new_item'       => __('Add New Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'new_item'           => __('New Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'edit_item'          => __('Edit Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'view_item'          => __('View Glossary Term', WP_Glossary_Hover::PLUGIN_SLUG),
			'all_items'          => __('All Glossary Terms', WP_Glossary_Hover::PLUGIN_SLUG),
			'search_items'       => __('Search Glossary Terms', WP_Glossary_Hover::PLUGIN_SLUG),
			'parent_item_colon'  => __('Parent Glossary Terms:', WP_Glossary_Hover::PLUGIN_SLUG),
			'not_found'          => __('No glossary terms found.', WP_Glossary_Hover::PLUGIN_SLUG),
			'not_found_in_trash' => __('No glossary terms found in Trash.', WP_Glossary_Hover::PLUGIN_SLUG),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'glossary'),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array('title', 'editor', 'author', 'revisions', 'comments')
		);

		register_post_type(WP_Glossary_Hover::GLOSSARY_POST_TYPE, $args);

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$settings = WPGH_Plugin_Config::get_settings();

		wp_enqueue_style(WP_Glossary_Hover::PLUGIN_SLUG . '-plugin-styles', plugins_url('assets/css/public.css', __FILE__), array(), self::VERSION);
		wp_enqueue_style(WP_Glossary_Hover::PLUGIN_SLUG . '-jquery-ui-styles', plugins_url('vendor/jquery-ui/css/'.$settings['styles_tooltip_theme'].'/jquery-ui-1.10.4.custom.min.css', __FILE__), array(), self::VERSION);

		$custom_css = "
		.wpgh-tooltip,
		a.wpgh-tooltip,
		a.wpgh-tooltip:hover,
		a.wpgh-tooltip:focus,
		.entry-content .wpgh-tooltip,
		.entry-content a.wpgh-tooltip,
		.entry-content a.wpgh-tooltip:hover,
		.entry-content a.wpgh-tooltip:focus {
			color: {$settings['styles_link_color']};
			border-style: {$settings['styles_link_underline']};
			border-bottom-width: 1px;
		}";
		wp_add_inline_style(WP_Glossary_Hover::PLUGIN_SLUG . '-plugin-styles', $custom_css);

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @see      http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$settings = WPGH_Plugin_Config::get_settings();

		// Register jquery ui scripts for tooltip widget
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-position');
		wp_enqueue_script('jquery-ui-tooltip');

		// Register effects for tooltip widget, based on settings
		wp_enqueue_script('jquery-effects-'.$settings['tooltip_hide_effect']);
		wp_enqueue_script('jquery-effects-'.$settings['tooltip_show_effect']);

		// Register script to create the tooltip for glossary terms
		wp_enqueue_script(WP_Glossary_Hover::PLUGIN_SLUG . '-plugin-script', plugins_url('assets/js/public.js', __FILE__), array('jquery'), self::VERSION);

		$tooltip_settings = array(
			'tooltip_general_track' => $settings['tooltip_general_track'],
			'tooltip_hide_enabled' => $settings['tooltip_hide_enabled'],
			'tooltip_hide_effect' => $settings['tooltip_hide_effect'],
			'tooltip_hide_delay' => $settings['tooltip_hide_delay'],
			'tooltip_hide_duration' => $settings['tooltip_hide_duration'],
			'tooltip_hide_easing' => $settings['tooltip_hide_easing'],
			'tooltip_show_enabled' => $settings['tooltip_show_enabled'],
			'tooltip_show_effect' => $settings['tooltip_show_effect'],
			'tooltip_show_delay' => $settings['tooltip_show_delay'],
			'tooltip_show_duration' => $settings['tooltip_show_duration'],
			'tooltip_show_easing' => $settings['tooltip_show_easing']
		);

		wp_localize_script(WP_Glossary_Hover::PLUGIN_SLUG . '-plugin-script', 'wp_glossary_hover_tooltip_settings', $tooltip_settings);

	}

}
