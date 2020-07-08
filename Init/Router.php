<?php 
if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use Phroute\Phroute\RouteCollector as Router;
use Phroute\Phroute\Dispatcher as Map;

$rewrite = Rewrite::get();
$rewrite = ucfirst($rewrite::requestGroup());
$router = new Router();
$router->filter('start', ['Http\\Filters\\Start', 'filter']);
$router->filter('complete', ['Http\\Filters\\Complete', 'filter']);

require_once(ROUTER.$rewrite.'.php');
return $router;