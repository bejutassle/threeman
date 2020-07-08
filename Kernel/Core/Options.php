<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Options Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use Core\Database as Database;
use Core\Cache as Cache;
use Illuminate\Support\Arr;
use \Rewrite as Rewrite;

class Options{

protected $db;
protected $array = array();
protected $cache;
protected $protocol;
protected $address;
protected $dir;

protected static $Instance = array();

public static function get() {
            $class = get_called_class();
            if(! isset(static::$Instance[$class])):
                  static::$Instance[$class] = new $class();
            endif;
            return static::$Instance[$class];
}

public function __construct(){
			$this->protocol = (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off') ? 'https://' : 'http://');
			$this->address = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : ''));
			$this->dir = '/';
			$this->db = Database::get();
            $this->cache = Cache::get();
            $this->cache->eraseExpired();
}

public function getOption($var = NULL){
	switch ($var) {
		case 'full-address':
			return vsprintf('%s%s%s', [
				$this->protocol, 
				$this->address, 
				$this->dir
			]);
		break;

		case 'base-address':
			return vsprintf('%s%s%s%s', [
				$this->protocol, 
				$this->address, 
				$this->dir,
				Rewrite::requestRegisterGroup()
			]);
		break;

		case 'admin-address':
			return vsprintf('%s%s%s%s/', [
				$this->protocol, 
				$this->address, 
				$this->dir,
				config('app.system.control')
			]);
		break;

		case 'site-address':
			return vsprintf('%s%s', [
				$this->address, 
				$this->dir
			]);
		break;

		case 'host-address':
			return vsprintf('%s%s', [
				$this->protocol, 
				$_SERVER['HTTP_HOST']
			]);
		break;

		case 'query-url':
			return fsprintf('{{protocol}}{{address}}{{query}}', [
				'protocol' => $this->protocol,
				'address' => $this->address,
				'query' => $_SERVER['REQUEST_URI'],
			]);
		break;

		case 'protocol-address':
			return fsprintf('{{protocol}}{{address}}', [
				'protocol' => $this->protocol,
				'address' => $this->address
			]);
		break;

		case 'site-container':
			return config('app.view.container');
		break;

		case 'json':
	
			$json['site']['name'] = option('site-name');
			$json['site']['lang'] = option('locale');
			$json['site']['address'] = $this->address;
			$json['site']['full_address'] = option('full-address');
			$json['site']['host_address'] = option('host-address');
			$json['site']['path'] = vsprintf('/%s/', [Rewrite::requestRegisterGroup()]);
			$json['site']['container'] = config('app.view.container');
			if(config('google.recaptcha.status') == 'true') $json['site']['recaptcha_key']  = config('google.recaptcha.sitekey');
			if(option('cookie-policy') == 1) $json['site']['cookie_policy_url']  = option('cookie-policy-url');

			return $json;
		break;
		
		default:

			if(in_array($var, ['protocol', 'address', 'dir'])){
				return $this->$var;
			}else{
				return $this->setOption($var);
			}
			
		break;
	}

}

public function setOption($name = NULL){
        $this->cache->setCache('site');

        if($this->cache->isCached('options')):
        $options = $this->cache->retrieve('options');
        else:
		$data = $this->db->get('options', NULL, 'name, COALESCE(value, "") AS value');
		$options = array_pluck($data, 'value', 'name');

        $this->cache->store('options', $options);
        $options = $this->cache->retrieve('options');
        endif;

	    return $options[$name];
}

private function setThemeSettings($type){
	$config = config('themes');

	if($type == 'page'):
	$this->db->where('themeType', $type);
	$this->db->where('themeStatus', 1);
	$theme = $this->db->getOne('themes');

	return $theme;

	elseif(!empty($config[$type])):
	$config = $config[$type];
	$key = array_search(1, array_column($config, 'themeStatus'));
	$config = $config[$key];
	$theme = [
		'themeID' => $config['themeID'],
		'themeName' => $config['themeName'],
		'themeDir' => $config['themeDir'],
		'themeAuthor' => $config['themeAuthor'],
		'themeHtmlMinify' => $config['themeHtmlMinify'],
		'themeHtmlCache' => $config['themeHtmlCache'],
		'themeCssMinify' => $config['themeCssMinify'],
		'themeCssCache' => $config['themeCssCache'],
		'themeJsMinify' => $config['themeJsMinify'],
		'themeJsCache' => $config['themeJsCache'],
		'themeType' => $config['themeType'],
		'themeStatus' => $config['themeStatus'],
		'themeVersion' => $config['themeVersion'],
	];

		return $theme;

	else:
		return exit(trans('no_theme_dumps_found!'));
	endif;
}

public function getSiteThemeSettings($type){
	return $this->setThemeSettings($type);
}


}