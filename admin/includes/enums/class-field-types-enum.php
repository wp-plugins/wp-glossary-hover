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
 * WP Glossary Hover Field Types Enum class. This class serves as an enum for field types.
 *
 * @package   WPGH_Field_Types_Enum
 * @author    Chris Horton <chorton2227@gmail.com>
 */
abstract class WPGH_Field_Types_Enum extends WPGH_Base_Enum {

    /**
     * Constant value for checkbox field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const Checkbox = 0;

    /**
     * Constant value for integer field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const Integer = 1;

    /**
     * Constant value for select field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const Select = 2;

    /**
     * Constant value for textbox field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const Textbox = 3;

    /**
     * Constant value for multi checkbox field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const MultiCheckbox = 4;

    /**
     * Constant value for color picker field type.
     *
     * @since    1.0.0
     * @var      integer
     */
    const ColorPicker = 5;

}