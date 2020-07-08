<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Config Core
 * @copyright  It is protected by the GNU General Public License
 * All Rights Reserved
 */

namespace Core;


class Config{
	
/**
 * @var array
 */
protected static $settings = array();


/**
 * Get the registered settings.
 * @return mixed|null
 */
public static function all(){
    	return static::$settings;
}

/**
 * Return true if the key exists.
 * @param string $key
 * @return bool
 */
public static function exists($key){
    	return ! is_null(array_get(static::$settings, $key));
}

/**
 * Get the value.
 * @param string $key
 * @return mixed|null
 */
public static function get($key, $default = null){
    	return array_get(static::$settings, $key, $default);
}

/**
 * Set the value.
 * @param string $key
 * @param mixed $value
 */
public static function set($key, $value){
    	array_set(static::$settings, $key, $value);
}

/**
 * Change the value.
 * @param string $key
 * @param mixed $value
 */
public static function change($key, $value){
    	data_set(static::$settings, $key, $value);
}

}