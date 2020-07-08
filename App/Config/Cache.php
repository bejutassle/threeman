<?php

use Core\Config;


Config::set('cache', [

    'template' => [
        'page' => CACHE.'view/facedes/p/',
        config('app.system.control') => CACHE.'view/facedes/a/',
        'mail' => CACHE.'view/facedes/m/',
    ],
    'assets' => [
        'page' => CACHE.'view/assets/p/',
        config('app.system.control') => CACHE.'view/assets/a/',
    ],
    'image' => [
        'global' => CACHE.'view/image/',
    ],
    'server'    =>  [
        array('127.0.0.1',11211,1),
    ],
    'memcache' => [
        array('127.0.0.1', 11211, 1),
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => '',
        'password' => '',
        'database' => '',
        'timeout' => '',
    ],
    'ssdb' => [
        'host' => '127.0.0.1',
        'port' => 8888,
        'password' => '',
        'timeout' => '',
    ],
    'locale' => [
        'name' => 'language',
        'path'      => CACHE.'data/locale/',
        'extension' => '.cache',
    ],
    'simple' => [
            'default' => [
            'name' => 'threeman',
            'path'      => CACHE.'data/',
            'extension' => '.cache',
        ],
        'page' => [
            'name' => 'threeman',
            'path'      => CACHE.'data/',
            'extension' => '.cache',
        ],
        'admin' => [
            'name' => 'threeman',
            'path'      => CACHE.'data/',
            'extension' => '.cache',
        ],
    ],
]);