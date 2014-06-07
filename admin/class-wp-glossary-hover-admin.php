<?php
/**
 * WP Glossary Hover Plugin
 *
 * @package   WP_Glossary_Hover_Admin
 * @author    Chris Horton <chorton2227@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/chorton2227/WP-Glossary-Hover
 * @copyright 2014 Chris Horton
 */

/**
 * WP Glossary Hover Admin class. This class works with administrative side of the WordPress site.
 *
 * @package   WP_Glossary_Hover_Admin
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WP_Glossary_Hover_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Settings for the plugin
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_settings = null;

	/**
	 * jQuery UI Effects
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $jquery_ui_effects = array(
		'blind',
		'bounce',
		'clip',
		'drop',
		'explode',
		'fade',
		'fold',
		'highlight',
		'pulsate',
		'scale',
		'shake',
		'slide',
		'transfer'
	);

	/**
	 * jQuery UI Easings
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $jquery_ui_easings = array(
		'linear',
		'swing',
		'easeInQuad',
		'easeOutQuad',
		'easeInOutQuad',
		'easeInCubic',
		'easeOutCubic',
		'easeInOutCubic',
		'easeInQuart',
		'easeOutQuart',
		'easeInOutQuart',
		'easeInQuint',
		'easeOutQuint',
		'easeInOutQuint',
		'easeInExpo',
		'easeOutExpo',
		'easeInOutExpo',
		'easeInSine',
		'easeOutSine',
		'easeInOutSine',
		'easeInCirc',
		'easeOutCirc',
		'easeInOutCirc',
		'easeInElastic',
		'easeOutElastic',
		'easeInOutElastic',
		'easeInBack',
		'easeOutBack',
		'easeInOutBack',
		'easeInBounce',
		'easeOutBounce',
		'easeInOutBounce'
	);

	/**
	 * jQuery UI Themes
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $jquery_ui_themes = array(
		'ui-lightness',
		'ui-darkness',
		'smoothness',
		'start',
		'redmond',
		'sunny',
		'overcast',
		'le-frog',
		'flick',
		'pepper-grinder',
		'eggplant',
		'dark-hive',
		'cupertino',
		'south-street',
		'blitzer',
		'humanity',
		'hot-sneaks',
		'excite-bike',
		'vader',
		'dot-luv',
		'mint-choc',
		'black-tie',
		'trontastic',
		'swanky-purse'
	);

	/**
	 * CSS Border Styles
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $css_border_styles = array(
		'dotted',
		'dashed',
		'solid',
		'double',
		'groove',
		'ridge',
		'inset',
		'outset'
	);

	/**
	 * Html tags that are disabled from being parsed by default.
	 * Individual tags can be enabled through the plugin settings.
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $disabled_tags = array(
		'a',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'pre',
		'object',
		'blockquote'
	);

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Glossary_Hover::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->glossary_post_type = $plugin->get_glossary_post_type();

		// Register plugin settings
		add_action('init', array($this, 'setup_plugin_settings'));

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
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__('WP Glossary Hover', $this->plugin_slug),
			__('WP Glossary Hover', $this->plugin_slug),
			'manage_options',
			$this->plugin_slug,
			array($this, 'display_plugin_admin_page')
		);

	}

	/**
	 * Register the plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function setup_plugin_settings() {

		$post_types = get_post_types();

		// Remove custom post type for this plugin
		if (isset($post_types[$this->glossary_post_type])) {
			unset($post_types[$this->glossary_post_type]);
		}

		// General Settings
		$general_section_fields = array();

		$general_section_fields[] = new WPGH_Checkbox_Field(
			$this->plugin_slug,
			'general_highlight_first_occurrence',
			__('Highlight First Occurrence Only', $this->plugin_slug),
			__('When enabled only highlight first occurrence of term.', $this->plugin_slug),
			false
		);

		$general_section_fields[] = new WPGH_Checkbox_Field(
			$this->plugin_slug,
			'general_case_sensitive',
			__('Case Sensitive', $this->plugin_slug),
			__('When enabled highlight terms are case sensitive.', $this->plugin_slug),
			true
		);

		$general_section_fields[] = new WPGH_Multi_Checkbox_Field(
			$this->plugin_slug,
			'general_enabled_post_types',
			__('Enabled Post Types', $this->plugin_slug),
			__('Whether the tooltip should track (follow) the mouse.', $this->plugin_slug),
			array('post'),
			$post_types
		);

		$general_section_fields[] = new WPGH_Multi_Checkbox_Field(
			$this->plugin_slug,
			'general_disabled_tags',
			__('Disabled Html Tags', $this->plugin_slug),
			__('When checked, tags are not parsed for glossary terms.', $this->plugin_slug),
			$this->disabled_tags,
			$this->disabled_tags
		);

		$general_section = new WPGH_Settings_Section(
			'general',
			__('General Settings', $this->plugin_slug),
			__('Basic settings for plugin behavior.', $this->plugin_slug),
			$general_section_fields
		);

		// Tooltip General Settings
		$tooltip_general_fields = array();

		$tooltip_general_fields[] = new WPGH_Checkbox_Field(
			$this->plugin_slug,
			'tooltip_general_track',
			__('Track', $this->plugin_slug),
			__('Whether the tooltip should track (follow) the mouse.', $this->plugin_slug),
			false
		);

		$tooltip_general_section = new WPGH_Settings_Section(
			'tooltip_general',
			__('General Settings', $this->plugin_slug),
			__('', $this->plugin_slug),
			$tooltip_general_fields
		);

		// Tooltip Hide Settings
		$tooltip_hide_fields = array();

		$tooltip_hide_fields[] = new WPGH_Checkbox_Field(
			$this->plugin_slug,
			'tooltip_hide_enabled',
			__('Enabled', $this->plugin_slug),
			__('When disabled, no animation will be used and the tooltip will be hidden immediately.', $this->plugin_slug),
			true
		);

		$tooltip_hide_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'tooltip_hide_effect',
			__('Effect', $this->plugin_slug),
			__('', $this->plugin_slug),
			'fade',
			$this->jquery_ui_effects
		);

		$tooltip_hide_fields[] = new WPGH_Integer_Field(
			$this->plugin_slug,
			'tooltip_hide_delay',
			__('Delay', $this->plugin_slug),
			__('milliseconds', $this->plugin_slug),
			0
		);

		$tooltip_hide_fields[] = new WPGH_Integer_Field(
			$this->plugin_slug,
			'tooltip_hide_duration',
			__('Duration', $this->plugin_slug),
			__('milliseconds', $this->plugin_slug),
			100
		);

		$tooltip_hide_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'tooltip_hide_easing',
			__('Easing', $this->plugin_slug),
			__('', $this->plugin_slug),
			'linear',
			$this->jquery_ui_easings
		);

		$tooltip_hide_section = new WPGH_Settings_Section(
			'tooltip_hide',
			__('Hide Settings', $this->plugin_slug),
			__('If and how to animate the hiding of the tooltip.', $this->plugin_slug),
			$tooltip_hide_fields
		);

		// Tooltip Show Settings
		$tooltip_show_fields = array();

		$tooltip_show_fields[] = new WPGH_Checkbox_Field(
			$this->plugin_slug,
			'tooltip_show_enabled',
			__('Enabled', $this->plugin_slug),
			__('When disabled, no animation will be used and the tooltip will be hidden immediately.', $this->plugin_slug),
			true
		);

		$tooltip_show_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'tooltip_show_effect',
			__('Effect', $this->plugin_slug),
			__('', $this->plugin_slug),
			'fade',
			$this->jquery_ui_effects
		);

		$tooltip_show_fields[] = new WPGH_Integer_Field(
			$this->plugin_slug,
			'tooltip_show_delay',
			__('Delay', $this->plugin_slug),
			__('milliseconds', $this->plugin_slug),
			0
		);

		$tooltip_show_fields[] = new WPGH_Integer_Field(
			$this->plugin_slug,
			'tooltip_show_duration',
			__('Duration', $this->plugin_slug),
			__('milliseconds', $this->plugin_slug),
			100
		);

		$tooltip_show_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'tooltip_show_easing',
			__('Easing', $this->plugin_slug),
			__('', $this->plugin_slug),
			'linear',
			$this->jquery_ui_easings
		);

		$tooltip_show_section = new WPGH_Settings_Section(
			'tooltip_show',
			__('Show Settings', $this->plugin_slug),
			__('If and how to animate the showing of the tooltip.', $this->plugin_slug),
			$tooltip_show_fields
		);

		// Styles Tooltip Settings
		$styles_tooltip_fields = array();

		$styles_tooltip_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'styles_tooltip_theme',
			__('Tooltip Theme', $this->plugin_slug),
			__('jQuery UI theme for tooltip.', $this->plugin_slug),
			'ui-lightness',
			$this->jquery_ui_themes
		);

		$styles_tooltip_section = new WPGH_Settings_Section(
			'styles_tooltip',
			__('Tooltip Styles', $this->plugin_slug),
			__('How the tooltip looks.', $this->plugin_slug),
			$styles_tooltip_fields
		);

		// Styles Link Settings
		$styles_link_fields = array();

		$styles_link_fields[] = new WPGH_Color_Picker_Field(
			$this->plugin_slug,
			'styles_link_color',
			__('Link Color', $this->plugin_slug),
			__('', $this->plugin_slug),
			'#ff0000'
		);

		$styles_link_fields[] = new WPGH_Select_Field(
			$this->plugin_slug,
			'styles_link_underline',
			__('Link Underline', $this->plugin_slug),
			__('', $this->plugin_slug),
			'dashed',
			$this->css_border_styles
		);

		$styles_link_section = new WPGH_Settings_Section(
			'styles_link',
			__('Link Styles', $this->plugin_slug),
			__('How the tooltip link looks on the page.', $this->plugin_slug),
			$styles_link_fields
		);

		// Setup Tabs
		$tabs = array();

		$tabs[] = new WPGH_Settings_Tab(
			'general',
			__('General', $this->plugin_slug),
			array($general_section)
		);

		$tabs[] = new WPGH_Settings_Tab(
			'tooltip',
			__('Tooltip', $this->plugin_slug),
			array($tooltip_general_section, $tooltip_hide_section, $tooltip_show_section)
		);

		$tabs[] = new WPGH_Settings_Tab(
			'styles',
			__('Styles', $this->plugin_slug),
			array($styles_tooltip_section, $styles_link_section)
		);

		// Register plugin settings
		$this->plugin_settings = new WPGH_Plugin_Settings(
			__('WP Glossary Hover', $this->plugin_slug),
			$this->plugin_slug,
			'manage_options',
			$tabs
		);

	}

}
