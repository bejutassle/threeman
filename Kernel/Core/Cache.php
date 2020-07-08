<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Cache Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use Rewrite as Rewrite;
use Core\Config as Config;
use Supplement\Cache as CacheInternal;


class Cache{

public static function get() {
	$routeGroup = Rewrite::requestGroup();
	$config = config('cache.simple');
	$internal = (!empty($config[$routeGroup])) ? $config[$routeGroup] : $config['default'];
	return new CacheInternal($internal);
}

public function __construct(){

}

public function __destruct(){

}


}