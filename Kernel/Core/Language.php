<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Language Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use DElfimov\Translate\Translate;
use DElfimov\Translate\Loader\PhpFilesLoader;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Language{

protected static $lang;
protected static $config;
protected static $loader;
protected static $logger;

public static function get($lpath = NULL) {
	static::$config = config('language');
	static::$loader = new PhpFilesLoader(LANGUAGE);
	$path = vsprintf('%s%s.%s', [LOGS, 'translate', 'log']);
	static::$logger = new Logger('translate');
	static::$logger->pushHandler(new StreamHandler($path, Logger::WARNING));

	static::$lang = new Translate(static::$loader, static::$config);
	static::$lang->setLanguage(option('locale'));
	static::$lang->setLogger(static::$logger);
}

public static function t($var = NULL){
	return static::$lang->t($var);
}

public static function trans($string = NULL, $x = NULL, $args = NULL){
	return static::$lang->plural($string, $x, $args);
}


}