<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Database Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use Database\MysqliDb as SQL;
use Database\dbObject as ORM;

class Database{

protected static $Instance = array();

public static function get() {
	$class = get_called_class();
	if(empty(static::$Instance[$class])):
			$config = config('database.connections.mysql');
			$cluster = config('app.developer.environment');
	      	static::$Instance[$class] = new SQL($config[$cluster]);
	endif;

	return static::$Instance[$class];
}

public function __construct(){

}

public function __destruct(){

}


}