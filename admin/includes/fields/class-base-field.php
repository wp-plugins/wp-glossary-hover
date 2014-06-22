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
 * WP Glossary Hover Base Field class. This class serves as a base for field classes.
 *
 * @package   WPGH_Base_Field
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Base_Field {

    /**
     * The name of the settings field in Wordpress Settings API.
     *
     * @see      http://codex.wordpress.org/Function_Reference/add_settings_field
     * @since    1.0.0
     * @var      string
     */
	public $setting_name = null;

    /**
     * The id used to setup a settings field in Wordpress Settings API.
     *
     * @see      http://codex.wordpress.org/Function_Reference/add_settings_field
     * @since    1.0.0
     * @var      string
     */
	public $id = null;

    /**
     * The title used to setup a settings field in Wordpress Settings API.
     *
     * @see      http://codex.wordpress.org/Function_Reference/add_settings_field
     * @since    1.0.0
     * @var      string
     */
	public $title = null;

    /**
     * The type of field. Used to determine how to display and validate field.
     *
     * @see      WPGH_Field_Types_Enum
     * @since    1.0.0
     * @var      integer
     */
	public $type = null;

	/**
	 * A description of field. Is displayed on options page, based on field $type.
	 *
     * @since    1.0.0
     * @var      string
	 */
	public $description = null;

	/**
	 * Default value of field.
	 *
	 * @since    1.0.0
	 * @var      mixed
	 */
	public $default = null;

	/**
	 * Set class variables.
	 *
	 * @param    string     $setting_name    Name of settings field.
	 * @param    string     $id              Unique id of settings field.
	 * @param    string     $title           Title of settings field.
	 * @param    integer    $type            Type of settings field, @see WPGH_Field_Types_Enum.
	 * @param    string     $description     Description of settings field.
	 * @param    mixed      $default         Default value of settings field.
	 * @since    1.0.0
	 */
	public function __construct($setting_name, $id, $title, $type, $description, $default) {

		$this->setting_name = $setting_name;
		$this->id = $id;
		$this->title = $title;
		$this->type = $type;
		$this->description = $description;
		$this->default = $default;

	}

	/**
	 * Empty method, should be overriden by extended class.
	 *
	 * Displays the settings field for page.
	 *
	 * @since    1.0.0
	 * @param    mixed    $value    Value of field.
	 */
	public function display_html($value) { }

	/**
	 * Empty method, should be overriden by extended class.
	 *
	 * Validate the settings field.
	 *
	 * @since     1.0.0
	 * @param     mixed    $value    Value of field.
	 * @return    mixed    Valid value for settings field
	 */
	public function validate($value) { }

	/**
	 * Empty method, should be overriden by extended class.
	 *
	 * Validate the settings field.
	 *
	 * @since     1.0.0
	 * @param     mixed    $value    Value of field.
	 * @return    mixed    Valid value for settings field
	 */
	protected function get_field_name() {

		return "$this->setting_name[$this->id]";

	}

}