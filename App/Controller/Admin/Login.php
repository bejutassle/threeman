<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Core\Mail as Mail;
use Mail\Send as SendMail;
use Helper\Router as Router;
use Carbon\Carbon as Time;
use \Rewrite as Rewrite;
use \Json as Json;

class Login extends Admin{

public function __construct(){
            parent::__construct();
}

public function getIndex(){
	 $this->user->authorized('unregistered');

	 Template::title(trans('admin.title.login'));
	 Template::view('login/', 'index');
}

public function postIndex(){
	$this->response->rule('xhr');
	$this->user->authorized('unregistered');
	$auth['ip'] = getRealIPAddress();
	$auth['key'] = uniqid();
	$auth['user_id'] = $this->db
	->where('userName', $this->request['username'])
	->getValue('users', 'userID');

	$this->validation->setRules('username', 'required');
	$this->validation->setRules('password', 'required|min:3');

	$has_user = $this->db
	->where('userName', $this->request['username'])
   	->where('userPassword', sha1($this->request['password']))
   	->where('userMod', 1)
   	->has('users');

	if(!$has_user){
		$this->validation->addErrors('email', 'missing');
		$this->validation->addErrors('password', 'missing');

		$this->db->insert('users_activity', [
			'userActivityUserID' => $auth['user_id'],
			'userActivityType' => 'login-password-error',
			'userActivityIP' => $this->db->func('INET_ATON(?)', [$auth['ip']]),
			'userActivityDate' => $this->db->now(),
		]);
	}

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.redirecting-login');
	$json['description'] = trans('information.redirecting');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

		$is_login = $this->db
		->where('userID', $auth['user_id'])
		->update('users', [
			'userLastLogin' => $this->db->now(),
			'userModAuth' => $auth['key']
		]);
		
		if($is_login){
			$this->user->login(true, $auth['user_id'], $auth['key']);

			$this->db->insert('users_activity', [
				'userActivityUserID' => $auth['user_id'],
				'userActivityType' => 'login-user-account',
				'userActivityIP' => $this->db->func('INET_ATON(?)', [$auth['ip']]),
				'userActivityDate' => $this->db->now(),
			]);
		}

	endif;
}

public function getLogout(){
		$this->user->authorized('registered');
		return $this->user->logout();
}

public function __destruct(){
		parent::__destruct();
}


}