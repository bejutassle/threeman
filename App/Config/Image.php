<?php 

use Core\Config;


/**
 * Setup the Image configuration.
 */
Config::set('image', [

        'driver' => class_exists('Imagick') ? 'imagick' : 'gd',
        'quality' => 100,
        'lifetime' => 100000,

]);