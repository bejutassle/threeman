<?php 

use Core\Config;


Config::set('event', [

    'connections' => [

        'beanstalkd' => [
            'host'  => '127.0.0.1',
            'port'  => 11300,
        ],

        'redis' => [
            'host' => '127.0.0.1',
            'port' => '',
            'password' => '',
            'database' => '',
            'timeout' => '',
        ],
    ],

]);