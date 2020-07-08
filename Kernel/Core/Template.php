<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Template Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Twig_Environment;
use Twig_Extension_Debug;
use Rewrite as Rewrite;
use Core\Database as Database;
use Core\Config as Config;
use Helper\Router as Router;
use Supplement\Template as TemplateBundle;
use Carbon\Carbon as Time;

class Template{

protected static $tpl;
protected static $mainTitle;
protected static $title_seperator;
protected static $file_system;

protected static $config = array();
protected static $dyn_var = array();

const HtmlDir = 'html';
const Ext = 'twig';

public static function config($var, $key){
	return self::$config[$var] = $key;
}

public static function addPath($path, $cache = NULL){
	$templateDir = $path.self::HtmlDir.'/';
	$routeGroup = Rewrite::requestGroup();
	$tplCache = config('cache.template');
	$configCache = (!empty($cache)) ? $cache : $tplCache[$routeGroup];
	$configArr = config('template');
	self::$dyn_var['tpl'] = $configArr;
	if($configArr['cache'] == true):
		$configArr['cache'] = $configCache;
	endif;

	static::$file_system = new Twig_Loader_Filesystem();
	static::$file_system->addPath($templateDir);
	static::$tpl = new Twig_Environment(static::$file_system, $configArr);

	if($configArr['debug'] == true){
	static::$tpl->addExtension(new Twig_Extension_Debug());
	}
	static::$tpl->addExtension(new TemplateBundle());
}

public static function append($var, $key){
	return self::$dyn_var[$var] = $key;
}

public static function mainTitle($var = NULL){
	$var = (!empty($var)) ? $var : '';
	return static::$mainTitle = $var;
}

public static function title($param){
	if(empty(static::$mainTitle)):
		$title = '%3$s';
	elseif(empty($param)):
		$title = '%1$s';
	else:

	if(option('placement') == 1):
		$title = '%3$s %2$s %1$s';
	else:
		$title = '%1$s %2$s %3$s';
	endif;

	endif;

	$returnf = sprintf($title, static::$mainTitle, option('separator'), $param);
	self::append('pagetitle', $param);
	return self::append('title', $returnf);
}

public static function widget(array $var){
	return self::append('widget', $var);
}

public static function view($dir, $param){
	if(empty(self::$dyn_var['title'])):
		self::append('title', static::$mainTitle);
	endif;

	if(empty(self::$dyn_var['widget'])):
		self::append('widget', ['slider' => false, 'submenu' => false]);
	endif;

	$return_tpl = sprintf('%1$s.%2$s', $dir.$param, self::Ext);
	return static::$tpl->display($return_tpl, self::$dyn_var);
}

public static function render($dir, $param){
	if(empty(self::$dyn_var['title'])):
		self::append('title', static::$mainTitle);
	endif;

	$return_tpl = sprintf('%1$s.%2$s', $dir.$param, self::Ext);
	return (string) static::$tpl->render($return_tpl, self::$dyn_var);
}


}