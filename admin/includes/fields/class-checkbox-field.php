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
 * WP Glossary Hover Checkbox Field class. This class serves as a checkbox settings field.
 *
 * @package   WPGH_Checkbox_Field
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Checkbox_Field extends WPGH_Base_Field {

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

		parent::__construct($setting_name, $id, $title, WPGH_Field_Types_Enum::Checkbox, $description, $default);

	}

	/**
	 * Displays the settings field for page.
	 *
	 * @since    1.0.0
	 * @param    mixed    $value    Value of field.
	 */
	public function display_html($value = FALSE) {

		?>

		<label for="<?php echo $this->get_field_name(); ?>">

			<input type="checkbox" id="<?php echo $this->get_field_name(); ?>" name="<?php echo $this->get_field_name(); ?>" value="1" <?php if ($value) echo 'checked="checked"'; ?>>

			<?php if ( ! empty($this->description)) : ?>

				<?php echo $this->description; ?>

			<?php endif; ?>

		</label>

		<?php

	}

	/**
	 * Validate the settings field.
	 *
	 * @since     1.0.0
	 * @param     mixed      $value    Value of field.
	 * @return    boolean    Valid value for settings field
	 */
	public function validate($value) {

		return (NULL !== $value && '1' === $value) ? true : false;

	}

}