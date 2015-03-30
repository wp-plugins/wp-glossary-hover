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
 * WP Glossary Hover Plugin Settings class. This class handles the plugin settings page.
 *
 * @package   WPGH_Plugin_Settings
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Plugin_Settings {

	/**
	 * The title used to setup an options page.
	 *
	 * @see      http://codex.wordpress.org/Function_Reference/add_options_page
	 * @since    1.0.0
	 * @var      string
	 */
	protected $title = null;

	/**
	 * The slug used to setup an options page and register the setting.
	 *
	 * @see      http://codex.wordpress.org/Function_Reference/add_options_page
	 * @see      http://codex.wordpress.org/Function_Reference/register_setting
	 * @since    1.0.0
	 * @var      string
	 */
	protected $slug = null;

	/**
	 * The capability used to setup an options page.
	 *
	 * @see      http://codex.wordpress.org/Function_Reference/add_options_page
	 * @since    1.0.0
	 * @var      string
	 */
	protected $capability = null;

	/**
	 * Settings for the plugin.
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $settings = null;

	/**
	 * Add plugin settings page and setup settings.
	 *
	 * @see      http://codex.wordpress.org/Function_Reference/add_action
	 * @see      http://codex.wordpress.org/Plugin_API/Action_Reference
	 * @param    string     $title         Title of settings plage.
	 * @param    string     $slug          Slug of settings page.
	 * @param    string     $capability    Description of settings page.
	 * @param    array      $settings      Plugin settings.
	 * @since    1.0.0
	 */
	public function __construct($title, $slug, $capability, $settings) {

		// Class variables
		$this->title = $title;
		$this->slug = $slug;
		$this->capability = $capability;
		$this->settings = $settings;

		// Add the options page and menu item.
		add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

		// Setup plugin settings
		add_action('admin_init', array($this, 'settings_init'), 9);

	}

	/**
	 * Returns array containing the settings for the plugin or false if settings not found.
	 *
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function get_settings() {

		return get_option($this->slug);

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			$this->title,
			$this->title,
			$this->capability,
			$this->slug,
			array($this, 'display_plugin_admin_page')
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		?>

		<div class="wrap">

			<h2><?php echo esc_html(get_admin_page_title()); ?></h2>

			<?php $this->display_settings_page_tabs(); ?>

			<form action="options.php" method="POST">

				<?php settings_fields($this->slug); ?>

				<?php $current_tab = $this->get_current_settings_page_tab(); ?>

				<?php foreach ($this->settings as $tab) : ?>

					<?php if ($current_tab != $tab->id) continue; ?>

					<?php foreach ($tab->sections as $section) : ?>

						<h3><?php echo $section->title; ?></h3>

						<?php echo $section->description; ?>

						<table class="form-table">

							<?php do_settings_fields($this->slug, $section->id); ?>

						</table><!-- /.form-table -->

					<?php endforeach; ?>

					<?php break; // Only one tab is active at a time ?>

				<?php endforeach; ?>

				<p class="submit">
					<input name="<?php echo $this->slug . '[' . $this->get_tab_submit_field_name($current_tab) . ']'; ?>" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', WP_Glossary_Hover::PLUGIN_SLUG); ?>" />
					<input name="<?php echo $this->slug . '[' . $this->get_tab_reset_field_name($current_tab) . ']'; ?>" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', WP_Glossary_Hover::PLUGIN_SLUG); ?>" />
				</p>

			</form>

		</div><!-- /.wrap -->

		<?php

	}

	/**
	 * Initialize plugins settings and setup settings via the Settings API.
	 *
	 * @since    1.0.0
	 */
	public function settings_init() {

		$update_settings = false;
		$settings = $this->get_settings();
		$default_settings = $this->get_default_settings();

		// Set plugin defaults
		if (false === $settings) {
			$settings = $default_settings;
			$update_settings = true;
		}
		// Add default values for missing settings
		else {
			foreach ($default_settings AS $key => $value)
			{
				if (!array_key_exists($key, $settings))
				{
					$settings[$key] = $value;
					$update_settings = true;
				}
			}
		}

		// Update settings
		if ($update_settings) {
			update_option($this->slug, $settings);
		}

		// Register plugin setting
		register_setting($this->slug, $this->slug, array($this, 'validate_settings'));

		foreach ($this->settings as $tab) {
			foreach ($tab->sections as $section) {
				// Register sections
				add_settings_section($section->id, $section->title, null, $this->slug);

				foreach ($section->fields as $field) {
					// Register fields
					add_settings_field($field->id, $field->title, array($field, 'display_html'), $this->slug, $section->id, $settings[$field->id]);
				}
			}
		}

	}

	/**
	 * Validation callback for plugin settings.
	 * Return validated settings to be saved.
	 *
	 * @since     1.0.0
	 * @return    array
	 */
	public function validate_settings($input) {		

		$valid_input = $this->get_settings();

		foreach ($this->settings as $tab) {
			$reset = false;
			$submit = false;

			// Tab was submitted
			if ( ! empty($input[$this->get_tab_submit_field_name($tab->id)])) {
				$submit = true;
			}
			// Tab was reset
			else if ( ! empty($input[$this->get_tab_reset_field_name($tab->id)])) {
				$reset = true;
			}

			// Tab not submitted or reset
			if ( ! $submit && ! $reset) {
				continue;
			}

			foreach ($tab->sections as $section) {
				foreach ($section->fields as $field) {
					$value = null;

					// Validate setting
					if ($submit) {
						$input_value = (array_key_exists($field->id, $input)) ? $input[$field->id] : null;
						$value = $field->validate($input_value);
					}
					// Reset setting
					else if ($reset) {
						$value = $field->default;
					}

					// Update setting
					if (null !== $value) {
						$valid_input[$field->id] = $value;
					}
				}
			}
		}

		return $valid_input;

	}

	/**
	 * Return array containing the default options for the plugin.
	 *
	 * @since     1.0.0
	 * @return    array
	 */
	private function get_default_settings() {

		$defaults = array();

		foreach ($this->settings as $tab) {
			foreach ($tab->sections as $section) {
				foreach ($section->fields as $field) {
					$defaults[$field->id] = $field->default;
				}
			}
		}

		return $defaults;

	}

	/**
	 * Render tabs for settings page.
	 *
	 * @since    1.0.0
	 */
	private function display_settings_page_tabs() {

		$current = $this->get_current_settings_page_tab();

		?>

		<h2 class="nav-tab-wrapper">

			<?php foreach ($this->settings as $tab) : ?>	

				<?php $class = 'nav-tab'; ?>
				<?php if ($tab->id == $current) $class .= ' nav-tab-active'; ?>

				<a class="<?php echo $class; ?>" href="?page=<?php echo $this->slug; ?>&tab=<?php echo $tab->id; ?>">
					<?php echo $tab->title; ?>
				</a>

			<?php endforeach; ?>

		</h2>

		<?php

	}

	/**
	 * Return the current tab on settings page.
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	private function get_current_settings_page_tab() {

		$current_tab = null;

		// Get current tab
		if (isset($_GET['tab'])) {
			$current_tab = $_GET['tab'];
		}

		foreach ($this->settings as $tab) {
			// Return first tab as default
			if (NULL === $current_tab) {
				return $tab->id;
			}

			// Validate $current_tab value
			if ($current_tab == $tab->id) {
				return $current_tab;
			}
		}

		// Valid current tab not found and default value not found
		return null;

    }

	/**
	 * Return the settings field name for tab submit button.
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	private function get_tab_submit_field_name($tab) {

    	return "submit-$tab";

    }

	/**
	 * Return the settings field name for tab reset button.
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	private function get_tab_reset_field_name($tab) {

    	return "reset-$tab";

    }

}
