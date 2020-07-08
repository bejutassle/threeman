<?php
if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

$router->group(['prefix' => config('app.system.control'), 'before' => 'start', 'after' => 'complete'], function($router){

            $router->group(['prefix' => 'img'], function($router){
                  $router->controller('/', 'Core\Image');
            });

            $router->group(['prefix' => 'css'], function($router){
                  $router->controller('/', 'Helper\Assets');
            });

            $router->group(['prefix' => 'js'], function($router){
                  $router->controller('/', 'Helper\Assets');
            });

            $router->controller('/', 'Admin\Home');
            $router->controller('login', 'Admin\Login');
            $router->controller('settings', 'Admin\Settings');
            $router->controller('user', 'Admin\User');
            $router->controller('page', 'Admin\Pages');
            $router->controller('sliders', 'Admin\Sliders');
            $router->controller('menu', 'Admin\Menu');
});