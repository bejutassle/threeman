<?php 

use Core\Config;


Config::set('router', [

'group' => [
    'page' => 'page',
    'admin' => config('app.system.control'),
    config('app.system.control') => config('app.system.control'),
],

'auth' => [
    'page' => 'user',
    config('app.system.control') => config('app.system.control'),
],

'exception' => [
	'notfound' => [
	    'page' => 'Page\PageNotFound',
	    config('app.system.control') => 'Admin\PageNotFound',
	],
],

]);