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

var_dump('test');
die();

// If uninstall not called from WordPress, then exit
if ( ! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}
if (is_multisite()) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	delete_option('wp-glossary-hover');
	if ($blogs) {
		foreach($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			delete_option('wp-glossary-hover');
			restore_current_blog();
		}
	}
}
else
{
	delete_option('wp-glossary-hover');
}