<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Template Supplement
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Supplement;

use Core\Config as Config;
use Core\Database as Database;
use Helper\Router as Router;
use Helper\Assets as Assets;
use Helper\Globals as Globals;
use Support\Str as Str;
use Carbon\Carbon as Time;

use Twig_ExpressionParser as Twig_ExpressionParser;
use Twig_Extension as Twig_Extension;
use Twig_SimpleFilter as Twig_Filter;
use Twig_SimpleFunction as Twig_Function;
use Twig_Extension_GlobalsInterface as Twig_Extension_GlobalsInterface;

class Template extends Twig_Extension implements Twig_Extension_GlobalsInterface{

protected $assets;

public function __construct(){
		$this->assets = new Assets();
}

public function getOperators(){
    return [
        [
            '!' => [
	            	'precedence' => 50, 
	            	'class' => 'Twig_Node_Expression_Unary_Not'
            		],
        ],
        [
            '||' => [
            		  'precedence' => 10, 
            		  'class' => 'Twig_Node_Expression_Binary_Or', 
            		  'associativity' => Twig_ExpressionParser::OPERATOR_LEFT
            		],
            '&&' => [
            		  'precedence' => 15, 
            		  'class' => 'Twig_Node_Expression_Binary_And', 
            		  'associativity' => Twig_ExpressionParser::OPERATOR_LEFT
            		],
        ],
    ];
}

public function getGlobals(){
    return [
        		'str' => new Str(),
        		'app' => new Globals(),
        		'csrf_token' => getSession('token'),
    	];
}

public function getFilters(){
	return [

		new \Twig_SimpleFilter('isset', 'isset'),
		new \Twig_SimpleFilter('empty', 'empty'),
		new \Twig_SimpleFilter('array_sum', 'array_sum'),
		new \Twig_SimpleFilter('json_decode', 'json_decode'),
		new \Twig_SimpleFilter('json_encode', 'json_encode'),
		new \Twig_SimpleFilter('serialize', 'serialize'),
		new \Twig_SimpleFilter('unserialize', 'unserialize'),
		new \Twig_SimpleFilter('base64_encode', 'base64_encode'),
		new \Twig_SimpleFilter('base64_decode', 'base64_decode'),
		new \Twig_SimpleFilter('urlencode', 'urlencode'),
		new \Twig_SimpleFilter('urldecode', 'urldecode'),
		new \Twig_SimpleFilter('htmlspecialchars_decode', 'htmlspecialchars_decode'),
		new \Twig_SimpleFilter('ucfirst', 'ucfirst'),
		new \Twig_SimpleFilter('lcfirst', 'lcfirst'),
		new \Twig_SimpleFilter('ucwords', 'ucwords'),

	    new Twig_Filter('dbytes', [
	    			$this, 
	    			'directoryBytesFilter'
	    	]),

	    new Twig_Filter('stylesheet', [
	    			$this, 
	    			'stylesheetFilter'
	    	]),

	    new Twig_Filter('javascript', [
	    			$this, 
	    			'javascriptFilter'
	    	]),

	    new Twig_Filter('t', [
	    			$this, 
	    			'Lang'
	    	]),

	    new Twig_Filter('BBCode', [
	    			$this, 
	    			'BBCode'
	    	]),
	];
}

public function getFunctions(){
	return [

	    new Twig_Function('assets', [
	    			$this, 
	    			'Assets'
	    	]),

	    new Twig_Function('url', [
	    			$this, 
	    			'URL'
	    	]),

	    new Twig_Function('img', [
	    			$this, 
	    			'Image'
	    	]),

	    new Twig_Function('lang', [
	    			$this, 
	    			'Lang'
	    	]),

	    new Twig_Function('encrypt', [
	    			$this, 
	    			'Encrypt'
	    	]),

	    new Twig_Function('decrypt', [
	    			$this, 
	    			'Decrypt'
	    	]),

	    new Twig_Function('config', [
	    			$this, 
	    			'config'
	    	]),

	    new Twig_Function('Time', [
	    			$this, 
	    			'Time'
	    	]),

	    new Twig_Function('session', [
	    			$this, 
	    			'getSession'
	    	]),

	    new Twig_Function('filemtime', [
	    			$this, 
	    			'filemtime'
	    	]),

	    new Twig_Function('locale_get_display_language', [
	    			$this, 
	    			'locale_get_display_language'
	    	]),

	    new Twig_Function('filter_var', [
	    			$this, 
	    			'filter_var'
	    	]),
	];
}

public function Assets($path = NULL, $url = false){
	return assets($path, $url);
}

public function URL($name, $args = NULL){
	return url($name, $args);
}

public function Image($img, $path = NULL, $width = NULL, $height = NULL, $quality = NULL){
	return Image($img, $path, $width, $height, $quality);
}

public function Lang($string, $x = NULL, $args = NULL){
	return trans($string, $x, $args);
}

public function Encrypt($val = NULL){
	return encrypt($val);
}

public function Decrypt($val = NULL){
	return decrypt($val);
}

public function BBCode($str = NULL, $core = 'parse'){
	return BBCode($str, $core);
}

public function Config($var = NULL){
	return config($var);
}

public function Time($date = NULL, $format = NULL, $ex = NULL){
		$args = func_get_args();

		if(empty($args)):
			return Time::now(option('time-zone'))
			->settings([
    		'locale' => option('locale'),
    		'timezone' => option('time-zone'),
			])->toDateTimeString();
		endif;

		if($format  == 'diff'):
    		return Time::parse($date)->diffForHumans();
    	elseif($format == 'localization'):
    		return Time::parse($date)->formatLocalized($ex);
    	elseif($format == 'manuel'):
    		return Time::parse($date)->format($ex);
    	elseif($format == 'javascript'):
    		return Time::parse($date)->toAtomString();
    	else:
    		return Time::parse($date)->format('m-d-Y G:i:s');
    	endif;
}

public function entityFilter($str = NULL){
    	return decodeHTMLEntity($str);
}

public function getSession($name = NULL){
	return getSession($name);
}

public function filemtime($file){
	if (file_exists($file)){
		return filemtime($file);
	}else{
		return 1526909724;
	}
}

public function directoryBytesFilter($path = NULL, $size = 0){
    return getDirectoryBytes($path, $size);
}

public function stylesheetFilter($tag, $files = array(), $filter = array(), $output = 'load'){
	return $this->assets->stylesheet($tag, $files, $filter, $output);
}

public function javascriptFilter($tag, $files = array(), $filter = array(), $output = 'load'){
	return $this->assets->javascript($tag, $files, $filter, $output);
}

public function locale_get_display_language($lang, $deflang = 'tr'){
	$deflang = config('language.default');
	return locale_get_display_language($lang, $deflang);
}

public function filter_var(...$vars){
	list($var, $filter) = $vars;

	if($filter == 'FILTER_VALIDATE_URL'){
		return filter_var($var, FILTER_VALIDATE_URL);
	}
}

}