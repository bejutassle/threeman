<?php 

use Core\Config;


/**
 * Setup the session configuration.
 */
Config::set('session', [

        'duration' => [
            'user' => 1440,
            config('app.system.control') => 1440,
        ],
        
        'auth' => [
            'user' => 'user_UVi3WVgomXC4X2A72s5QdJjXlwGxkPDl',
            config('app.system.control') => vsprintf('%1s_%2s', [config('app.system.control'), 'vlvK3bMvRAVG2SCPc0hjVuzFOnhbM0Z3']),
        ],

        'middleware' => [
            'user' => '\Middleware\User',
            config('app.system.control') => '\Middleware\Admin',
        ],

]);