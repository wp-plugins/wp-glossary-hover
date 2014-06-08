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
 * WP Glossary Hover Settings Tab class. This class serves to hold settings tab information.
 *
 * @package   WPGH_Settings_Tab
 * @author    Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Settings_Tab {

    /**
     * The id used to setup tab on settings page.
     *
     * @since    1.0.0
     * @var      string
     */
	public $id = null;

    /**
     * The title used to setup tab on settings page.
     *
     * @since    1.0.0
     * @var      string
     */
	public $title = null;

    /**
     * The sections used to setup a settings tab in Wordpress Settings API.
     *
     * @see      http://codex.wordpress.org/Function_Reference/add_settings_section
     * @since    1.0.0
     * @var      array
     */
	public $sections = null;

	/**
	 * Set class variables.
	 *
	 * @param    string     $id          Unique id of settings tab.
	 * @param    string     $title       Title of settings tab.
	 * @param    array      $sections    Sections of settings tab.
	 * @since    1.0.0
	 */
	public function __construct($id, $title, $sections) {

		$this->id = $id;
		$this->title = $title;
		$this->sections = $sections;

	}

}