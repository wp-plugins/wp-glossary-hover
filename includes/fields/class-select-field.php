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
 * WP Glossary Hover Select Field class. This class serves as a select settings field.
 *
 * @package   WPGH_Select_Field
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Select_Field extends WPGH_Base_Field {

    /**
     * The options for the select field.
     *
     * @see      WPGH_Field_Types_Enum
     * @since    1.0.0
     * @var      integer
     */
	public $options = null;

	/**
	 * Set class variables.
	 *
	 * @see      WPGH_Field_Types_Enum
	 * @param    string     $setting_name    Name of settings field.
	 * @param    string     $id              Unique id of settings field.
	 * @param    string     $title           Title of settings field.
	 * @param    string     $description     Description of settings field.
	 * @param    mixed      $default         Default value of settings field.
	 * @param    array      $options         Array of possible options for settings field.
	 * @since    1.0.0
	 */
	public function __construct($setting_name, $id, $title, $description, $default, $options) {

		parent::__construct($setting_name, $id, $title, WPGH_Field_Types_Enum::Select, $description, $default);
		$this->options = $options;

	}

	/**
	 * Displays the settings field for page.
	 *
	 * @since    1.0.0
	 * @param    mixed    $value    Value of field.
	 */
	public function display_html($value = '') {

		?>

		<select id="<?php echo $this->get_field_name(); ?>" name="<?php echo $this->get_field_name(); ?>">

			<?php foreach ($this->options as $option) : ?>

				<option value="<?php echo $option; ?>" <?php if ($value === $option) echo 'selected="selected"'; ?>>

					<?php echo $option; ?>

				</option>

			<?php endforeach; ?>

		</select>

		<?php

		if ( ! empty($this->description)) {
			echo $this->description;
		}

	}

	/**
	 * Validate the settings field.
	 *
	 * @since     1.0.0
	 * @param     mixed         $value    Value of field.
	 * @return    mixed|null    Valid value for settings field or null if invalid
	 */
	public function validate($value) {

		if (in_array($value, $this->options))
		{
			return $value;
		}

		return null;

	}

}