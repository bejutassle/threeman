<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Url Rewrite Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use Phroute\Phroute\Dispatcher as Map;

class Rewrite{

protected static $Instance = array();
protected $request;
protected $response;

public static function get() {
            $class = get_called_class();
            if(! isset(static::$Instance[$class])):
                  static::$Instance[$class] = new $class();
            endif;

            return static::$Instance[$class];
}

public static function setRewrite(){
			return require_once(INIT.'Router.php');
}

public function getRewrite(){
			$dispatcher = new Map($this->setRewrite()->getData());
			$requestMethod = $this->requestMethod();
			$requestUri = $this->requestUri();
			$response = $dispatcher->dispatch($requestMethod, $requestUri);
			
			return $response;
}

private function requestMethod(){
			if(!empty($_SERVER['HTTP_X_PJAX'])){
			header("X-PJAX-URL: {$_SERVER['REQUEST_URI']}");
			}

			$requestMethod = $_SERVER['REQUEST_METHOD'];

			return $requestMethod; 
}

private function requestUri(){
			$parseRequestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

			return $parseRequestUrl; 
}

public static function requestMap(){
			header('HTTP/1.1 404 Not Found');
			$e = self::requestGroupException('notfound');
			$c = new $e;

			return $c->index();
}

public static function removeParam($url) {

	if($url){
		if(!empty(config('http.filter.currentlist.find'))){
			$url = rtrim(preg_replace(config('http.filter.currentlist.find'), config('http.filter.currentlist.replace'), $url), '?');
		}
	}

    return preg_replace('#&+$#', '', $url);
}

public static function requestCurrent(){
		if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
		     (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
		     (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
		    $_SERVER["REQUEST_SCHEME"] = 'https';
		} else {
		    $_SERVER["REQUEST_SCHEME"] = 'http';
		}
        $currentURL['protocol'] = sprintf('%s://', $_SERVER["REQUEST_SCHEME"]);
        $currentURL['address'] = $_SERVER["SERVER_NAME"];
       	$currentURL['query'] = (!empty($_SERVER['REQUEST_URI'])) ? self::removeParam($_SERVER['REQUEST_URI']) : '';

        return join('', $currentURL);
}

public static function requestCurrentPath($path = NULL){
		$currentURL = (!empty($path)) ? $path : current(array_filter(explode('/',parse_url(self::requestCurrent(), PHP_URL_PATH))));

		return $currentURL;
}

public static function requestGroup(){
		$group = config('router.group');
		$path = self::requestCurrentPath();

		return (!empty($group[$path])) ? $group[$path] : $group['page'];
}

public static function requestRegisterGroup(){
		$group = config('router.group');
		$path = self::requestCurrentPath();

		return (!empty($group[$path])) ? $group[$path] : '';
}

public static function requestUserGroup(){
		$group = config('router.auth');
		$path = self::requestCurrentPath();

		return (!empty($group[$path])) ? $group[$path] : $group['page'];
}

public static function requestGroupException($param = 'notfound'){
		$group = config('router.exception');
		$group = $group[$param];
		$path = self::requestCurrentPath();

		return (!empty($group[$path])) ? $group[$path] : $group['page'];
}

public static function requestMiddlewareGroup(){
	  	$type = self::requestUserGroup();
	  	$middleware = config('session.middleware');
	  	$type = (!empty($middleware[$type])) ? $type : array_keys($middleware)[0];
	  	$middleware = (!empty($middleware[$type])) ? $middleware[$type] : reset($middleware);
	  	$middleware = $middleware::get($type);

	  	return $middleware;
}

public static function route($name, array $args = null){
			$router = include(INIT.'Router.php');
			
			return $router->route($name, $args);
}

public static function redirect($location, $status = 302){
        if(!empty($_SERVER['HTTP_X_PJAX'])) {
            header('X-PJAX-URL: '.$location, true);
            exit();
        }
        
        header('Location: '.$location, true, $status);
        exit();
}

}