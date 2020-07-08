<?php 

use Core\Config;


Config::set('language', [
       
		'default' => option('locale'),
        'default-language' => option('locale'),
        'available' => [
            'tr',
        ],
]);