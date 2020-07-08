<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package WebApp Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use Logger\Log;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;


class App{

public static function Run(){
	try{
		Rewrite::get()->getRewrite();
	}catch(Exception $e){
		(config('app.developer.environment') == 'local') ? print('<table>'.$e->xdebug_message.'</table>') : Rewrite::get()->requestMap();
		$logger = new Log('exception', Log::ERROR);
		$logger->error('Exception caught', $e->getTrace());
	}catch(HttpRouteNotFoundException $e){
		(config('app.developer.environment') == 'local') ? print('<table>'.$e->xdebug_message.'</table>') : false;
	}catch(HttpMethodNotAllowedException $e){
		(config('app.developer.environment') == 'local') ? print('<table>'.$e->xdebug_message.'</table>') : false;
	}
}


}