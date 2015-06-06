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
 * WPGH Tooltip Parser class. Parse html, adding definition tooltip on golssary words and phrases.
 *
 * @package	WPGH_Tooltip_Parser
 * @author	Chris Horton <chorton2227@gmail.com>
 */
class WPGH_Tooltip_Parser {

	/**
	 * Custom post type for glossary terms.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $post_type = null;

	/**
	 * Array containing disabled html tags.
	 *
	 * @since    1.0.0
	 * @var      array
	 */
	protected $disabled_tags = array(
		'script',
		'code',
		'strike'
	);

	/**
	 * XPath query for selecting text nodes.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $xpath_query = '//text()';

	/**
	 * XPath query string format used to exclude certain tags from query results.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $xpath_disable_tag_query = '[not(ancestor::%s)]';

	/**
	 * Regex string format used to find glossary terms.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $term_regex_pattern = '/(\b%s\b)(?![^<]*>|[^<>]*<\/)/';

	/**
	 * String format used when limiting the number of characters.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	protected $tooltip_more = '%s...';

	/**
	 * String format for creating tooltip span tag.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	protected $tooltip_span_html = '<span class="wpgh-tooltip" title="%s">%s</span>';

	/**
	 * String format for creating tooltip link tag.
	 *
	 * @since    1.2.0
	 * @var      string
	 */
	protected $tooltip_link_html = '<a class="wpgh-tooltip" href="%s" title="%s">%s</a>';

	/**
	 * The encoding used when converting characters. 
	 *
	 * @since    1.2.1
	 * @var      string
	 */
	protected $encoding = 'UTF-8';

	/**
	 * Regex string pattern to trim whitespace and punctuation from end of definition.
	 *
	 * @since    1.2.1
	 * @var      string
	 */
	protected $trim_regex_pattern = '/[^\w\d]+$/m';

	/**
	 * Initialize the plugin by setting the post type.
	 *
	 * @since     1.0.0
	 */
	public function __construct($post_type) {

		$this->post_type = $post_type;

	}
	/**
	 * Returns WP_Query object with glossary terms.
	 *
	 * @since    1.0.0
	 * @return   object
	 */
	public function get_glossary_terms() {

		$args = array(
			'post_type' => $this->post_type,
			'posts_per_page' => -1
		);

		return new WP_Query($args);

	}

	/**
	 * Parse content, adding glossary tooltips.
	 *
	 * @see      http://www.php.net/manual/en/class.domdocument.php
	 * @see      http://www.php.net/manual/en/class.domxpath.php
	 * @see      http://www.php.net/manual/en/ref.libxml.php
	 * @since    1.0.0
	 * @param    string    $content    Post content.
	 * @return   string
	 */
	public function parse_content($content) {

		// Must contain content
		if (empty($content))
		{
			return $content;
		}

		$settings = WPGH_Plugin_Config::get_settings();

		// Valid post type required
		if ( ! in_array(get_post_type(), $settings['general_enabled_post_types']))
		{
			return $content;
		}

		$terms = $this->get_glossary_terms();

		// No terms found
		if ( ! $terms->have_posts())
		{
			return $content;
		}

		// Enable user error handling
		// Catch bad html markup errors
		libxml_use_internal_errors(true);

		// Create DOMDocument object for content
		$dom = new DOMDocument();

		// Convert content to properly handle UTF-8 encoding
		$html = mb_convert_encoding($content, 'HTML-ENTITIES', $this->encoding);

		// Html must load correctly to allow parsing
		if (FALSE === $dom->loadHtml($html))
		{
			return $content;
		}

		// Retrieve html data
		$xpath = new DOMXPath($dom);
		$nodes = $xpath->query($this->get_xpath_query());

		// Expression is malformed or contextnode is invalid
		if (FALSE === $nodes)
		{
			return $content;
		}

		// Keep track of highlighted terms
		$highlight_first_occurrence = $settings['general_highlight_first_occurrence'];
		$highlighted_terms = array();

		// Check each node for glossary terms and add tooltip
		foreach ($nodes as $node)
		{
			$updateNode = false;
			$nodeValue = $node->nodeValue;

			foreach ($terms->posts as $term)
			{
				// If highlight first occurence setting is enabled
				// Check if current term has already been highlighted
				// If so continue to next term
				if ($highlight_first_occurrence && in_array($term->ID, $highlighted_terms))
				{
					continue;
				}

				// Check for glossary term
				$pattern = $this->get_term_regex_pattern($term->post_title);
				if (preg_match($pattern, $node->nodeValue) !== 1)
				{
					continue; // Not found, continue to next term
				}

				// If highlight first occurence setting is enabled limit preg_replace
				$replace_limit = ($highlight_first_occurrence) ? 1 : -1;

				// Add tooltip
				$tooltip = $this->get_tooltip_html($term->ID, $term->post_title, $term->post_content);
				$nodeValue = preg_replace($pattern, $tooltip, $nodeValue, $replace_limit);
				$updateNode = true; // Node needs to be updated

				// If highlight first occurence setting is enabled
				// Keep track of highlighted terms
				if ($highlight_first_occurrence)
				{
					$highlighted_terms[] = $term->ID;
				}
			}

			// Value changed, update node value
			if ($updateNode)
			{
				$newNode = $dom->createCDATASection($nodeValue);
				$node->parentNode->replaceChild($newNode, $node);
			}
		}

		// Reset libxml errors
		libxml_clear_errors();
		libxml_use_internal_errors(false);

		// Return updated html with tooltips
		$html = $dom->saveHTML();
		$html = $this->cleanHTML($html);
		return $html;

	}

	/**
	 * Clean the html. Remove the DOCTYPE. Remove the <html> and <body> tags.
	 *
	 * @since     1.2.1
	 * @param     string    $html    The html to clean.
	 * @return    string
	 */
	private function cleanHTML($html) {

		$html = preg_replace('/^<!DOCTYPE.+?>/', '', $html);
		$html = str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $html);
		return $html;

	}

	/**
	 * Returns xpath query for selecting text nodes to parse.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_xpath_query() {

		$settings = WPGH_Plugin_Config::get_settings();

		// Html tags to disable in xpath query
		$disabled_tags = array_merge($this->disabled_tags, $settings['general_disabled_tags']);

		// Default query
		$query = $this->xpath_query;

		// Add xpath queries for disabling tags
		foreach ($disabled_tags as $tag)
		{
			$query .= sprintf($this->xpath_disable_tag_query, $tag);
		}

		return $query;

	}

	/**
	 * Returns regex pattern for finding glossary term.
	 *
	 * @since    1.0.0
	 * @param    string    $term    Term to search for.
	 * @return   string
	 */
	private function get_term_regex_pattern($term) {
		
		$settings = WPGH_Plugin_Config::get_settings();

		$pattern = $this->term_regex_pattern;

		// Add 'i' after pattern for case insensitive search
		if ( ! $settings['general_case_sensitive'])
		{
			$pattern .= 'i';
		}

		return sprintf($pattern, $term);

	}

	/**
	 * Returns html for tooltip.
	 *
	 * @since    1.0.0
	 * @param    string    $id            Term Post ID.
	 * @param    string    $term          Glossary Term.
	 * @param    string    $definition    Definition of glossary term.
	 * @return   string
	 */
	private function get_tooltip_html($id, $term, $definition) {

		$settings = WPGH_Plugin_Config::get_settings();
		$link = $settings['tooltip_general_link'];
		$definition = $this->clean_definition($definition);
		
		if ($link)
		{
			$permalink = get_permalink($id);
			return sprintf($this->tooltip_link_html, $permalink, $definition, $term);
		}

		return sprintf($this->tooltip_span_html, $definition, $term);

	}

	/**
	 * Strip all tags and limit the number characters in the definition, if required.
	 * Encode double and single quotes.
	 *
	 * @see      http://php.net/en/htmlentities
	 * @since    1.2.0
	 * @param    string    $definition    Definition of glossary term.
	 * @return   string
	 */
	private function clean_definition($definition) {

		// Remove any html tags
		$definition = strip_tags($definition);

		// Limit characters
		$definition = $this->limit_characters_in_definition($definition);

		// Encode double and single quotes
		$definition = htmlentities($definition, ENT_QUOTES, $this->encoding);

		return $definition;

	}

	/**
	 * Limit characters in definition, based on setting and length of definition.
	 * If definition needs to be limited, trim all non-alphanumeric characters
	 * from the end of the definition and append the more formatting.
	 *
	 * @since    1.2.0
	 * @param    string    $definition    Definition of glossary term.
	 * @return   string
	 */
	private function limit_characters_in_definition($definition) {
		
		$settings = WPGH_Plugin_Config::get_settings();
		$limit_characters = $settings['tooltip_general_limit_characters'];

		// Check if limit characters setting has been set
		// Check if the current definition is longer than the set limit
		if ($limit_characters > 0 && strlen($definition) > $limit_characters)
		{
			// Limit number of characters in definition
			$definition = substr($definition, 0, $limit_characters);

			// Trim any non-alphanumeric characters from end of definition
			$definition = preg_replace($this->trim_regex_pattern, '', $definition);

			// Append the more formatting
			$definition = sprintf($this->tooltip_more, $definition);
		}

		return $definition;

	}

}
