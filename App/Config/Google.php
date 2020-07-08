<?php 

use Core\Config;


Config::set('google', [

        'recaptcha' => [
			  'status' => option('recaptcha'),
			  'code' => option('recaptcha-code'),
			  'secret' => option('recaptcha-secret'),
			  'sitekey' => option('recaptcha-site-key'),
			  'url' => option('recaptcha-url'),
        ],

        'analytics' => [
        	  'code' => option('google-analytics'),
        ],

]);