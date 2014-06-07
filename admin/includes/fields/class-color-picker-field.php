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
 * WP Glossary Hover Textbox Field class. This class serves as a color picker settings field.
 *
 * @package   WPGH_Color_Picker_Field
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Color_Picker_Field extends WPGH_Base_Field {

	/**
	 * Set class variables.
	 *
	 * @see      WPGH_Field_Types_Enum
	 * @param    string     $setting_name    Name of settings field.
	 * @param    string     $id              Unique id of settings field.
	 * @param    string     $title           Title of settings field.
	 * @param    string     $description     Description of settings field.
	 * @param    mixed      $default         Default value of settings field.
	 * @since    1.0.0
	 */
	public function __construct($setting_name, $id, $title, $description, $default) {

		parent::__construct($setting_name, $id, $title, WPGH_Field_Types_Enum::ColorPicker, $description, $default);

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Glossary_Hover::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load style sheet and javascript for color picker
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

	}

	/**
	 * Displays the settings field for page.
	 *
	 * @since    1.0.0
	 * @param    mixed    $value    Value of field.
	 */
	public function display_html($value = '') {

		?>

		<input type="text" class="wpgh-color-field" id="<?php echo $this->get_field_name(); ?>" name="<?php echo $this->get_field_name(); ?>" value="<?php echo $value; ?>">

		<?php

		if ( ! empty($this->description)) {
			echo $this->description;
		}
		
	}

	/**
	 * Validate the settings field.
	 *
	 * @since     1.0.0
	 * @param     mixed     $value    Value of field.
	 * @return    string    Valid value for settings field or null if invalid
	 */
	public function validate($value) {

		$value = strip_tags(stripslashes($value));

		if (FALSE === $this->check_color($value)) {
			add_settings_error($this->id, 'invalid-color', sprintf(__('Enter a valid color for %s', $this->plugin_slug), $this->title), 'error');

			$value = null;
		}

		return $value;

	}

	/**
	 * Register and enqueue color picker style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style('wp-color-picker');

	}

	/**
	 * Register and enqueues color picker JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script($this->plugin_slug . '-color-picker', plugins_url('../../assets/js/color-picker.js', __FILE__), array('wp-color-picker'), WP_Glossary_Hover::VERSION, true);

	}

	/**
	 * Check if value is a valid HEX color.
	 *
	 * @since     1.0.0
	 * @param     string     $value    Value of field.
	 * @return    boolean
	 */
	private function check_color($value) {

		if (preg_match('/^#[a-f0-9]{6}$/i', $value)) {
			return true;
		}

		return false;
	}

}