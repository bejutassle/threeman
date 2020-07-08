<?php
if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

$router->group(['prefix' => '/', 'before' => 'start', 'after' => 'complete'], function($router){

            $router->group(['prefix' => 'img'], function($router){
                  $router->controller('/', 'Core\Image');
            });

            $router->group(['prefix' => 'css'], function($router){
                  $router->controller('/', 'Helper\Assets');
            });

            $router->group(['prefix' => 'js'], function($router){
                  $router->controller('/', 'Helper\Assets');
            });

            $router->controller('/', 'Page\Home');
            $router->controller('contact', 'Page\Contact');
});