<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Start Filter
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Http\Filters;

use Rewrite as Rewrite;

class Start{

protected static $filters = [];

public function __construct(){

if(Rewrite::requestGroup() == 'page'){
	array_push(self::$filters, 'Http\Filters\Package\Maintenance');
}

if(Rewrite::requestGroup() == config('app.system.control')){
	array_push(self::$filters, 'Http\Filters\Package\Authority');
}

/*'Http\Filters\Package\Redirect', */
}

public function filter(){
	foreach (static::$filters as $filter) {
	  $obj = new $filter();
	  call_user_func(array($obj, 'filter'));
	}
}

}