<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Core\Mail as Mail;
use Helper\Router as Router;
use Helper\BBCode as BBCode;
use Support\Str as Str;
use Support\File as File;
use \Json as Json;

class Settings extends Admin{

public function __construct(){
        parent::__construct();
        $this->user->authorized('registered');
}

public function getIndex(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.general')]));
        Template::view('main/', 'settings/main');
}

public function getSeo(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.seo')]));
        Template::view('main/', 'settings/seo');
}

public function getGoogle(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.google')]));
		Template::view('main/', 'settings/google');
}

public function getMail(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.mail')]));
		Template::view('main/', 'settings/mail');
}

public function getContact(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.contact')]));
		Template::view('main/', 'settings/contact');
}

public function getCache(){

		$data = File::finder()->directories()
		->depth(0)
		->in(CACHE)
		->notPath('view')
		->sortByName();

		$view = File::finder()->directories()
		->depth(1)
		->in(CACHE)
		->notPath('data')
		->sortByName();

		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.cache')]));
		Template::append('datafiles', $data);
		Template::append('viewfiles', $view);
		Template::view('main/', 'settings/cache');
}

public function getMaintenance(){
		Template::title(trans('admin.title.settings-page', ['name' => trans('admin.maintenance')]));
		Template::view('main/', 'settings/maintenance');
}

public function postIndex(){
		$this->response->rule('xhr');

		if($this->request['form'] == 'main'):
		$this->validation->setRules('site-name', 'required|min:3');
		/*$this->validation->setRules('protocol', 'required|min:3');
		$this->validation->setRules('address', 'required|min:1');
		$this->validation->setRules('dir', 'required|min:1');*/
		$this->validation->setRules('locale', 'required');
		$this->validation->setRules('time-zone', 'required');
		$this->validation->setRules('week-start', 'required');
		$this->validation->setRules('cookie-policy-url', 'required_if:cookie-policy,1|min:1');
		$this->validation->setRules('logo', 'uploaded_file|max_size:2MB|mimes:png,svg');
		elseif($this->request['form'] == 'seo'):
		$this->validation->setRules('title', 'required|min:1|max:70');
		$this->validation->setRules('description', 'required|min:1|max:200');
		$this->validation->setRules('separator', 'required|min:1|max:1');
		elseif($this->request['form'] == 'google'):
		$this->validation->setRules('recaptcha-code', 'required_if:recaptcha,true|min:3');
		$this->validation->setRules('recaptcha-secret', 'required_if:recaptcha,true');
		$this->validation->setRules('recaptcha-site-key', 'required_if:recaptcha,true');
		$this->validation->setRules('recaptcha-url', 'required_if:recaptcha,true|url');
		elseif($this->request['form'] == 'mail'):
		$this->validation->setRules('mail-host', 'required_if:mail-type,smtp|min:1');
		$this->validation->setRules('mail-timeout', 'required_if:mail-type,smtp|integer');
		$this->validation->setRules('mail-user', 'required_if:mail-type,smtp|email');
		$this->validation->setRules('mail-password-r', 'required_if:mail-type,smtp|min:3');
		$this->validation->setRules('mail-sender', 'required|email');
		elseif($this->request['form'] == 'maintenance'):
		$this->validation->setRules('maintenance-content', 'required_if:maintenance,1');
		elseif($this->request['form'] == 'announcement'):
		$this->validation->setRules('announcement-content', 'required_if:announcement,1');
		else:
		endif;


		if($this->validation->errors()):
		$json['success'] = false;
		$json['message'] = $this->validation->errors();
		else:
		$json['success'] = true;
		$json['url'] = url('current');
		$json['message'] = trans('information.successful!');
		$json['description'] = trans('information.successful-update');
		endif;

		$json = Json::response($json, $this->request, $this->token);

		if(Json::message() == true):

		if(!empty($this->request['keywords'])){
			$this->request['keywords'] = join(',', $this->request['keywords']);
		}

		if(!empty($this->request['quantity'])){
			$this->request['quantity'] = join(',', $this->request['quantity']);
		}

		if(!empty($this->request['mail-password-r']) && $this->request['mail-password-r'] !== 'random'){
			$this->request['mail-password'] = encrypt($this->request['mail-password-r']);
		}

		//Send form data
		foreach ($this->request as $key => $value) {
				if($this->db->where('name', $key)->has('options') && !is_array($value)):
					$value = ($value == '') ? NULL : $value;

					$this->db->where('name', $key);
					$this->db->update('options', ['value' => $value]);
				endif;
		}
		//end

		//Send logo image
		if(!empty($this->request['logo']['tmp_name'])):
			$logo = $this->image->upload($this->request['logo'], media('site/'));
				if(!empty($logo['name'])):
					$this->db->where('name', 'logo');
					$this->db->update('options', ['value' => $logo['name']]);
				endif;
		endif;
		//end

		clearCache('site', 'options');
		endif;
}

public function postDelete(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if(!empty($rules)):

	$json['success'] = false;
	$json['message'] = $rules;

	else:

	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-delete');

	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

			File::file()->remove(fsprintf('{{folder}}{{path}}/', [
				'folder' => CACHE,
				'path' => str_replace('\\', '/', $hash),
			]));

	endif;
}

public function __destruct(){
		parent::__destruct();
}


}