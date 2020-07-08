<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Globals Helper
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Helper;

use Support\Str as Str;
use Middleware\Base as Middleware;

class Globals{

protected $user;

public function __construct(){
  	 $this->user = Middleware::get();
}

public function user($param = NULL){
      return $this->user->data($param);
}

public function is($param = NULL){
      return $this->user->is($param);
}

public function option($var = NULL){
	  return option($var);
}

public function config($param = NULL){
      return config($param);
}

public function server($param = NULL){
      return (!empty($_SERVER[$param])) ? $_SERVER[$param] : NULL;
}

public function request($param = NULL){
      return (!empty($_REQUEST[$param])) ? $_REQUEST[$param] : NULL;
}

}