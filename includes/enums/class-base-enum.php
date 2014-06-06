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
 * WP Glossary Hover Base Enum class. This class serves as a base for enum type classes.
 * Includes helper methods is_valid_name() and is_valid_value().
 *
 * http://stackoverflow.com/questions/254514/php-and-enumerations
 *
 * @package   WPGH_Base_Enum
 * @author    Chris Horton <chorton2227@gmail.com>
 */
abstract class WPGH_Base_Enum {

    /**
     * Array containing class 
     *
     * @since    1.0.0
     * @var      array
     */
    private static $_const_cache = NULL;

    /**
     * Use Reflections to retrieve all class constants.
     * Store results in class variable $_const_cache.
     * Only retrieve and store results once, afterwords
     * return results of $_const_cache directly.
     *
     * @see       http://www.php.net/manual/en/reflectionclass.getconstants.php
     * @since     1.0.0
     * @return    array    Contains class contants. Constant name in key, constant value in value. 
     */
    private static function _get_constants() {

        // Get class constants, if not cached
        if (self::$_const_cache === NULL) {
            $reflect = new ReflectionClass(get_called_class());
            self::$_const_cache = $reflect->getConstants();
        }

        return self::$_const_cache;

    }

    /**
     * Check if $name is a constant of this class.
     * If $strict is false, use strtolower() to check for match.
     * Return true if constant match was found, otherwise return false.
     *
     * @see       WPGH_Base_Enum::_get_constants()
     * @since     1.0.0
     * @param     string     $name      Name of constant.
     * @param     boolean    $strict    Use strict checking?
     * @return    boolean
     */
    public static function is_valid_name($name, $strict = FALSE) {

        $constants = self::_get_constants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);

    }

    /**
     * Check if $value is a value of a constant of this class.
     * Return true if value match was found, otherwise return false.
     *
     * @see       WPGH_Base_Enum::_get_constants()
     * @since     1.0.0
     * @param     string     $value    Value of enum.
     * @return    boolean
     */
    public static function is_valid_value($value) {

        $values = array_values(self::_get_constants());
        return in_array($value, $values, TRUE); // strict checking turned on
        
    }

}