<?php
/**
 * WP Glossary Hover Plugin
 *
 * @package   WPGH_Plugin_Config
 * @author    Chris Horton <chorton2227@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/chorton2227/WP-Glossary-Hover
 * @copyright 2014 Chris Horton
 */

/**
 * WP Glossary Hover Plugin Config class. This class contains plugin configurations.
 *
 * @package   WPGH_Plugin_Config
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Plugin_Config {

	/**
	 * Settings for the plugin.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	public static $plugin_settings = null;

	/**
	 * Setting tabs for the plugin.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	protected static $plugin_setting_tabs = null;

	/**
	 * Instance of this class.
	 *
	 * @since    1.2.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * jQuery UI Effects
	 *
	 * @since    1.2.0
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
	 * @since    1.2.0
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
	 * @since    1.2.0
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
	 * @since    1.2.0
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
		'outset',
		'none'
	);

	/**
	 * Html tags that are disabled from being parsed by default.
	 * Individual tags can be enabled through the plugin settings.
	 *
	 * @since    1.2.0
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
	 * Initialize plugin configuration.
	 *
	 * @since     1.2.0
	 */
	private function __construct() {

		$this->setup_plugin_tabs();

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.2.0
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
	 * Returns array containing the settings for the plugin.
	 *
	 * @since    1.2.0
	 * @return   array
	 */
	public static function get_settings() {

		$default_settings = self::get_default_settings();
		$settings = get_option(WP_Glossary_Hover::PLUGIN_SLUG);

		foreach ($default_settings as $key => $value)
		{
			if (!array_key_exists($key, $settings))
			{
				$settings[$key] = $value;
			}
		}

		return $settings;

	}

	/**
	 * Return array containing the default options for the plugin.
	 *
	 * @since     1.2.0
	 * @return    array
	 */
	public static function get_default_settings() {

		if (null == self::$plugin_setting_tabs) {
			return null;
		}

		$defaults = array();

		foreach (self::$plugin_setting_tabs as $tab) {
			foreach ($tab->sections as $section) {
				foreach ($section->fields as $field) {
					$defaults[$field->id] = $field->default;
				}
			}
		}

		return $defaults;
	}

	/**
	 * Register plugin settings.
	 *
	 * @since     1.2.0
	 */
	public static function register_plugin_settings() {

		// Register plugin settings
		self::$plugin_settings = new WPGH_Plugin_Settings(
			__('WP Glossary Hover', WP_Glossary_Hover::PLUGIN_SLUG),
			WP_Glossary_Hover::PLUGIN_SLUG,
			'manage_options',
			self::$plugin_setting_tabs
		);

	}

	/**
	 * Setup plugin setting tabs.
	 *
	 * @since    1.2.0
	 */
	private function setup_plugin_tabs() {

		$post_types = get_post_types();

		// Remove custom post type for this plugin
		if (isset($post_types[WP_Glossary_Hover::GLOSSARY_POST_TYPE])) {
			unset($post_types[WP_Glossary_Hover::GLOSSARY_POST_TYPE]);
		}

		// General Settings
		$general_section_fields = array();

		$general_section_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'general_highlight_first_occurrence',
			__('Highlight First Occurrence Only', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When enabled only highlight first occurrence of term.', WP_Glossary_Hover::PLUGIN_SLUG),
			false
		);

		$general_section_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'general_case_sensitive',
			__('Case Sensitive', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When enabled highlight terms are case sensitive.', WP_Glossary_Hover::PLUGIN_SLUG),
			true
		);

		$general_section_fields[] = new WPGH_Multi_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'general_enabled_post_types',
			__('Enabled Post Types', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When checked, the post type items will be parsed for glossary terms.', WP_Glossary_Hover::PLUGIN_SLUG),
			array('post'),
			$post_types
		);

		$general_section_fields[] = new WPGH_Multi_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'general_disabled_tags',
			__('Disabled Html Tags', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When checked, tags are not parsed for glossary terms.', WP_Glossary_Hover::PLUGIN_SLUG),
			$this->disabled_tags,
			$this->disabled_tags
		);

		$general_section = new WPGH_Settings_Section(
			'general',
			__('General Settings', WP_Glossary_Hover::PLUGIN_SLUG),
			__('Basic settings for plugin behavior.', WP_Glossary_Hover::PLUGIN_SLUG),
			$general_section_fields
		);

		// Tooltip General Settings
		$tooltip_general_fields = array();

		$tooltip_general_fields[] = new WPGH_Integer_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_general_limit_characters',
			__('Limit number of characters', WP_Glossary_Hover::PLUGIN_SLUG),
			__('characters <em>(Disabled when set to 0)</em>', WP_Glossary_Hover::PLUGIN_SLUG),
			0
		);

		$tooltip_general_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_general_link',
			__('Link to glossary term page', WP_Glossary_Hover::PLUGIN_SLUG),
			__('Whether the glossary term should link to the glossary term page.', WP_Glossary_Hover::PLUGIN_SLUG),
			false
		);

		$tooltip_general_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_general_track',
			__('Track', WP_Glossary_Hover::PLUGIN_SLUG),
			__('Whether the tooltip should track (follow) the mouse.', WP_Glossary_Hover::PLUGIN_SLUG),
			false
		);

		$tooltip_general_section = new WPGH_Settings_Section(
			'tooltip_general',
			__('General Settings', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			$tooltip_general_fields
		);

		// Tooltip Hide Settings
		$tooltip_hide_fields = array();

		$tooltip_hide_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_hide_enabled',
			__('Enabled', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When disabled, no animation will be used and the tooltip will be hidden immediately.', WP_Glossary_Hover::PLUGIN_SLUG),
			true
		);

		$tooltip_hide_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_hide_effect',
			__('Effect', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'fade',
			$this->jquery_ui_effects
		);

		$tooltip_hide_fields[] = new WPGH_Integer_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_hide_delay',
			__('Delay', WP_Glossary_Hover::PLUGIN_SLUG),
			__('milliseconds', WP_Glossary_Hover::PLUGIN_SLUG),
			0
		);

		$tooltip_hide_fields[] = new WPGH_Integer_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_hide_duration',
			__('Duration', WP_Glossary_Hover::PLUGIN_SLUG),
			__('milliseconds', WP_Glossary_Hover::PLUGIN_SLUG),
			100
		);

		$tooltip_hide_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_hide_easing',
			__('Easing', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'linear',
			$this->jquery_ui_easings
		);

		$tooltip_hide_section = new WPGH_Settings_Section(
			'tooltip_hide',
			__('Hide Settings', WP_Glossary_Hover::PLUGIN_SLUG),
			__('If and how to animate the hiding of the tooltip.', WP_Glossary_Hover::PLUGIN_SLUG),
			$tooltip_hide_fields
		);

		// Tooltip Show Settings
		$tooltip_show_fields = array();

		$tooltip_show_fields[] = new WPGH_Checkbox_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_show_enabled',
			__('Enabled', WP_Glossary_Hover::PLUGIN_SLUG),
			__('When disabled, no animation will be used and the tooltip will be hidden immediately.', WP_Glossary_Hover::PLUGIN_SLUG),
			true
		);

		$tooltip_show_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_show_effect',
			__('Effect', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'fade',
			$this->jquery_ui_effects
		);

		$tooltip_show_fields[] = new WPGH_Integer_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_show_delay',
			__('Delay', WP_Glossary_Hover::PLUGIN_SLUG),
			__('milliseconds', WP_Glossary_Hover::PLUGIN_SLUG),
			0
		);

		$tooltip_show_fields[] = new WPGH_Integer_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_show_duration',
			__('Duration', WP_Glossary_Hover::PLUGIN_SLUG),
			__('milliseconds', WP_Glossary_Hover::PLUGIN_SLUG),
			100
		);

		$tooltip_show_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'tooltip_show_easing',
			__('Easing', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'linear',
			$this->jquery_ui_easings
		);

		$tooltip_show_section = new WPGH_Settings_Section(
			'tooltip_show',
			__('Show Settings', WP_Glossary_Hover::PLUGIN_SLUG),
			__('If and how to animate the showing of the tooltip.', WP_Glossary_Hover::PLUGIN_SLUG),
			$tooltip_show_fields
		);

		// Styles Tooltip Settings
		$styles_tooltip_fields = array();

		$styles_tooltip_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'styles_tooltip_theme',
			__('Tooltip Theme', WP_Glossary_Hover::PLUGIN_SLUG),
			__('jQuery UI theme for tooltip.', WP_Glossary_Hover::PLUGIN_SLUG),
			'ui-lightness',
			$this->jquery_ui_themes
		);

		$styles_tooltip_section = new WPGH_Settings_Section(
			'styles_tooltip',
			__('Tooltip Styles', WP_Glossary_Hover::PLUGIN_SLUG),
			__('How the tooltip looks.', WP_Glossary_Hover::PLUGIN_SLUG),
			$styles_tooltip_fields
		);

		// Styles Link Settings
		$styles_link_fields = array();

		$styles_link_fields[] = new WPGH_Color_Picker_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'styles_link_color',
			__('Link Color', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'#ff0000'
		);

		$styles_link_fields[] = new WPGH_Select_Field(
			WP_Glossary_Hover::PLUGIN_SLUG,
			'styles_link_underline',
			__('Link Underline', WP_Glossary_Hover::PLUGIN_SLUG),
			'',
			'dashed',
			$this->css_border_styles
		);

		$styles_link_section = new WPGH_Settings_Section(
			'styles_link',
			__('Link Styles', WP_Glossary_Hover::PLUGIN_SLUG),
			__('How the tooltip link looks on the page.', WP_Glossary_Hover::PLUGIN_SLUG),
			$styles_link_fields
		);

		// Setup Tabs
		$tabs = array();

		$tabs[] = new WPGH_Settings_Tab(
			'general',
			__('General', WP_Glossary_Hover::PLUGIN_SLUG),
			array($general_section)
		);

		$tabs[] = new WPGH_Settings_Tab(
			'tooltip',
			__('Tooltip', WP_Glossary_Hover::PLUGIN_SLUG),
			array($tooltip_general_section, $tooltip_hide_section, $tooltip_show_section)
		);

		$tabs[] = new WPGH_Settings_Tab(
			'styles',
			__('Styles', WP_Glossary_Hover::PLUGIN_SLUG),
			array($styles_tooltip_section, $styles_link_section)
		);

		// Save tab settings
		self::$plugin_setting_tabs = $tabs;

	}

}
