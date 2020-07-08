<?php 

use Core\Config;


Config::set('http', [

	'base' => url(),
	'origin' => [
	    'allow' => [
	        '127.0.0.1',
	        'localhost',
	    ],
	],
	'filter' => [
		'whitelist' => [
			'img',
			'css',
			'js',
			'login',
			'logout',
			'?_pjax='.config('app.view.container'),
		],
		'blacklist' => [
			'page',
			'_pjax',
			'?_pjax='.config('app.view.container'),
			'REQUEST_METHOD',
			'previousPage',
			'PHPSESSID',
		],
		'currentlist' => [
			'find' => [
				'/_pjax=(.*)/',
				'/p=(.*)/',
				'/a=(.*)/',
				'/w=(.*)/',
				'/q=(.*)/',
				'/f=(.*)/',
				'/l=(.*)/',
			],
			'replace' => [
				'',
				'',
				'',
				'',
				'',
				'',
				'',
			],
		],
		'url_files' => [
			'.pdf',
			'.txt',
			'.psd',
			'.ai',
		],
	],

]);