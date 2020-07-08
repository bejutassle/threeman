<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Admin Middleware
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Middleware;

use \Core\Config as Config;
use \Core\Database as Database;
use \Helper\Router as Router;
use \Supplement\Cookie as Cookie;
use \Supplement\Session as Session;
use Settings as Settings;
use Rewrite as Rewrite;

class Admin{

protected static $Instance = array();
protected $session;
protected $cookie;
protected $settings;
protected $db;
protected $config;
protected $table = 'users';
protected $mod = 'userModAuth';
public static $userID;
public static $userAuth;
public static $userMod;

public static function get($type = 'user') {
			$type = config('app.system.control');
            $class = get_called_class();
            if(! isset(static::$Instance[$class])):
                  static::$Instance[$class] = new $class($type);
            endif;
            return static::$Instance[$class];
}

public function __construct($type = 'user'){
	$this->db = Database::get();
	$this->session = new Session();
	$this->config = config('session');
	static::$userMod = $type;
}

public function login($value, $id, $hash){
	$name = $this->config['auth'][static::$userMod];
	$user[static::$userMod]['login'] = $value;
	$user[static::$userMod]['id'] = $id;
	$user[static::$userMod]['hash'] = $hash;
	$this->session->register($this->config['duration'][static::$userMod]);
	$this->session->set($name, $user);
}

public function check(){
	$name = $this->config['auth'][static::$userMod];
	$userLogin = (!empty($this->session->get($name)[static::$userMod]['login'])) ? $this->session->get($name)[static::$userMod]['login'] : false;
	$userID = (!empty($this->session->get($name)[static::$userMod]['id'])) ? $this->session->get($name)[static::$userMod]['id'] : false;
	$userAuth = (!empty($this->session->get($name)[static::$userMod]['hash'])) ? $this->session->get($name)[static::$userMod]['hash'] : false;
	$auth = $this->auth();

	$boolean = ( ($userLogin == true) && ($auth != false) ) ? true : false;

	($boolean == false) ? $this->session->end($name) : '';

	return $boolean;
}

public function logout(){
	$name = $this->config['auth'][static::$userMod];
	$bool = $this->check();
	$redirect = url('');

	if($bool == true):
	$this->session->end($name);
	redirect($redirect);
	else:
	redirect($redirect);
	endif;
}

public function is_register(){
	$bool = $this->check();
	$redirect = url('login');

	if($bool == false):
	redirect($redirect);
	endif;
}

public function is_guest(){
	$bool = $this->check();
	$redirect = url(config('app.system.control'));

	if($bool == true):
	redirect($redirect);
	endif;
}

public function auth(){
	$name = $this->config['auth'][static::$userMod];
	$userLogin = (!empty($this->session->get($name)[static::$userMod]['login'])) ? $this->session->get($name)[static::$userMod]['login'] : false;
	$userID = (!empty($this->session->get($name)[static::$userMod]['id'])) ? $this->session->get($name)[static::$userMod]['id'] : false;
	$userAuth = (!empty($this->session->get($name)[static::$userMod]['hash'])) ? $this->session->get($name)[static::$userMod]['hash'] : false;

	$this->db->where('userID', $userID);
	$this->db->where($this->mod, $userAuth);
	$this->db->where('userStatus', 1);
	$user = $this->db->getOne($this->table.' AS U', 'U.*, CONCAT(userFirstName," ",userLastName) AS userFullName');

	return (!empty($user)) ? $user : false;
}

public function data($key = NULL){
	$array = $this->auth();

	return (!empty($array[$key])) ? $array[$key] : NULL;
}

public function group($key = NULL){
	$groupID = $this->data('userGroup');

	$this->db->where('userGroupID', $groupID);
	return $this->db->getValue('users_group', $key);
}

public function is($type = NULL){
	if($type == 'login'):
		$return = $this->check();
	elseif($type == 'mod' || $type == 'admin'):
		$return = (!empty($this->data($this->mod))) ? true : false;
	else:
		$return = NULL;
	endif;

		return $return;
}

public function authorized($param = NULL){
	return ($param == 'registered') ? $this->is_register() : $this->is_guest();
}

public function pageNotFound(){
    return Rewrite::get()->requestMap();
}

}