<?php 
use Core\Config;

Config::set('themes', [

        'panel' => [
                ['themeID' => 1,
                'themeName' => 'SB Admin 2',
                'themeDir' => 'sb-admin',
                'themeAuthor' => 'StartBootstrap',
                'themeHtmlMinify' => 0,
                'themeHtmlCache' => 1,
                'themeCssMinify' => 1,
                'themeCssCache' => 1,
                'themeJsMinify' => 1,
                'themeJsCache' => 1,
                'themeType' => 'panel',
                'themeStatus' => 1,
                'themeVersion' => '1.0']
        ],

        'mail' => [
                'themeID' => 1,
                'themeName' => 'Standart',
                'themeDir' => 'standart',
                'themeAuthor' => 'Emre Emir',
                'themeHtmlMinify' => 0,
                'themeHtmlCache' => 1,
                'themeCssMinify' => 1,
                'themeCssCache' => 1,
                'themeJsMinify' => 1,
                'themeJsCache' => 1,
                'themeType' => 'mail',
                'themeStatus' => 1,
                'themeVersion' => '1.0',
        ],

]);

Config::set('template', [

        'debug' => (bool) isDebug(),
        'charset' => 'UTF-8',
        'cache' => (bool) tplSettings('HtmlCache'),
        'strict_variables' => true,
        'autoescape' => false,
        'auto_reload' => true,
        'minify' => (bool) tplSettings('HtmlMinify'),
]);