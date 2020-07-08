<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Support\Str as Str;
use \Json as Json;

class Sliders extends Admin{

public function __construct(){
	parent::__construct();
	$this->user->authorized('registered');
}

public function getIndex(){
	$page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	$query = (!empty($this->request['q'])) ? $this->request['q'] : '';
	$filter = (!empty($this->request['f'])) ? explode(',', $this->request['f']) : [];
    $where = (!empty($this->request['w'])) ? array_map('html_entity_decode', explode(',', $this->request['w'])) : [];
    $limit = (!empty($this->request['l'])) ? ($this->request['l'] == 'all') ? 9999999 : $this->request['l'] : 10;
	$all = (!empty($this->request['a'])) ? explode(',', $this->request['a']) : [];

	$where['col'] = (count($where) == 2) ? array_shift($where) : 'sliderID';
	$where['var'] = (count($where) == 2) ? array_shift($where) : 0;
	$where['op'] = ($where['col'] == 'sliderID') ? '!=' : '=';
	$filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'sliderDate';
	$filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'DESC';

	if(!empty($query)):
	$this->db->where('sliderName', '%'.$query.'%', 'LIKE');
	endif;
	$this->db->where($where['col'], $where['var'], $where['op']);
	$this->db->orderBy($filter['col'], $filter['var']);
	$slider = $this->db->withTotalCount()->get('slider', [($page - 1) * $limit, $limit]);
	$totalItems = $this->db->totalCount;

	$pageList['totalItems'] = $totalItems;
	$pageList['totalPages'] = ceil($totalItems / $limit);
	$pageList['currentPage'] = $page;
	$pageList['currentFilters'] = url('current', [parseQuery($this->request)]);
	$pageList['pageSplit'] = '';
	$pageList['startListPageLimit'] = 1;
	$pageList['viewPageLimit'] = 1;
	$pageList['showFirstAndLastPage'] = true;

	$sliderTypes = ['main', 'wide', 'column'];

	Template::title(trans('admin.title.management-page', ['name' => trans('admin.slider')]));
	Template::append('sliders', $slider);
	Template::append('sliderTypes', $sliderTypes);
	Template::append('page', $pageList);
	Template::view('main/sliders/', 'list');
}

public function getSort(){
	$page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	$query = (!empty($this->request['q'])) ? $this->request['q'] : '';
	$filter = (!empty($this->request['f'])) ? explode(',', $this->request['f']) : [];
    $where = (!empty($this->request['w'])) ? array_map('html_entity_decode', explode(',', $this->request['w'])) : [];
    $limit = (!empty($this->request['l'])) ? ($this->request['l'] == 'all') ? 9999999 : $this->request['l'] : 10;
	$all = (!empty($this->request['a'])) ? explode(',', $this->request['a']) : [];

	$where['col'] = (count($where) == 2) ? array_shift($where) : 'sliderID';
	$where['var'] = (count($where) == 2) ? array_shift($where) : NULL;
	$where['op'] = ($where['col'] == 'sliderID') ? '!=' : '=';
	$filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'sliderSort';
	$filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'ASC';

	$this->db->where($where['col'], $where['var'], $where['op']);
	$this->db->orderBy($filter['col'], $filter['var']);
	$sliderList = $this->db->get('slider', NULL, 'sliderID, sliderName');
	$sliderTypes = ['main', 'wide', 'column'];

	Template::title(trans('admin.title.sort-page', ['name' => trans('admin.slider')]));
	Template::append('sliders', $sliderList);
	Template::append('sliderTypes', $sliderTypes);
	Template::view('main/sliders/', 'sort');
}

public function getAdd(){
	$sliderType = ['main', 'wide', 'column'];
	$sliderFloat = ['center', 'left', 'right'];

	Template::title(trans('admin.title.management-page', ['name' => trans('admin.slider')]));
	Template::append('sliderType', $sliderType);
	Template::append('sliderFloat', $sliderFloat);
	Template::view('main/sliders/', 'add');
}

public function getEdit(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->db->where('sliderID', $hash);
	$slider = $this->db->getOne('slider');

	$sliderType = ['main', 'wide', 'column'];
	$sliderFloat = ['center', 'left', 'right'];

	if(empty($slider)):
		return $this->pageNotFound();
	endif;

	Template::title(trans('admin.title.edit-page', ['name' => trans('admin.slider')]));
	Template::append('slider', $slider);
	Template::append('sliderType', $sliderType);
	Template::append('sliderFloat', $sliderFloat);
	Template::view('main/sliders/', 'edit');
}

public function postUnpublished(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-passive');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db
	->where('sliderID', $hash)
	->update('slider', [
		'sliderStatus' => 0
	]);

	endif;
}

public function postPublished(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-activate');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db
	->where('sliderID', $hash)
	->update('slider', [
		'sliderStatus' => 1
	]);

	endif;
}

public function postDelete(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-delete');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db
	->where('sliderID', $hash)
	->delete('slider');

	endif;
}

public function postEdit(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->validation->setRules('name', 'required|min:3');
	$this->validation->setRules('type', 'required');
	$this->validation->setRules('url', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_float', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_bg_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_border_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('image', 'uploaded_file|max_size:5MB|mimes:png,jpg,jpeg,gif');

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

	$this->db
	->where('sliderID', $hash)
	->update('slider', [
		'sliderName' => $this->request['name'],
		'sliderTitle' => $this->request['title'],
		'sliderText' => $this->request['text'],
		'sliderType' => $this->request['type'],
		'sliderURL' => $this->request['url'],
		'sliderButtonStatus' => $this->request['button_status'],
		'sliderButtonText' => $this->request['button_text'],
		'sliderButtonFloat' => $this->request['button_float'],
		'sliderButtonColor' => $this->request['button_color'],
		'sliderButtonBgColor' => $this->request['button_bg_color'],
		'sliderButtonBorderColor' => $this->request['button_border_color'],
		'sliderDate' => $this->db->now(),
		'sliderStatus' => $this->request['status'],
	]);

	if(!empty($this->request['image']['tmp_name'])){

        $file = fsprintf('{{filename}}.{{ext}}', [
            'filename' => slug($this->request['name'], '-', [
				'table' => 'slider', 
				'column' => 'sliderImg',
			]),
            'ext' => 'jpg'
        ]);

	    $filename = fsprintf('{{path}}{{folder}}{{file}}', [
	        'path' => MEDIA,
	        'folder' => 'img/sliders/',
	        'file' => $file,
	    ]);

	    $this->image->save($this->request['image']['tmp_name'], $filename);

		$this->db
		->where('sliderID', $hash)
		->update('slider', [
			'sliderImg' => $file
		]);
	}

	clearGroupCache('sliders');

	endif;
}

public function postAdd(){
	$this->response->rule('xhr');

	$this->validation->setRules('name', 'required|min:3');
	$this->validation->setRules('type', 'required');
	$this->validation->setRules('url', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_float', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_bg_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('button_border_color', 'required_if:button_status,true|min:1');
	$this->validation->setRules('image', 'required|uploaded_file|max_size:5MB|mimes:png,jpg,jpeg,gif');

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('sliders');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-insert');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$sliderID = $this->db
	->insert('slider', [
		'sliderName' => $this->request['name'],
		'sliderTitle' => $this->request['title'],
		'sliderText' => $this->request['text'],
		'sliderType' => $this->request['type'],
		'sliderURL' => $this->request['url'],
		'sliderButtonStatus' => $this->request['button_status'],
		'sliderButtonText' => $this->request['button_text'],
		'sliderButtonFloat' => $this->request['button_float'],
		'sliderButtonColor' => $this->request['button_color'],
		'sliderButtonBgColor' => $this->request['button_bg_color'],
		'sliderButtonBorderColor' => $this->request['button_border_color'],
		'sliderDate' => $this->db->now(),
		'sliderStatus' => $this->request['status'],
	]);

	if(!empty($this->request['image']['tmp_name'])){

        $file = fsprintf('{{filename}}.{{ext}}', [
            'filename' => slug($this->request['name'], '-', [
				'table' => 'slider', 
				'column' => 'sliderImg',
			]),
            'ext' => 'jpg'
        ]);

	    $filename = fsprintf('{{path}}{{folder}}{{file}}', [
	        'path' => MEDIA,
	        'folder' => 'img/sliders/',
	        'file' => $file,
	    ]);

	    $this->image->save($this->request['image']['tmp_name'], $filename);

		$this->db
		->where('sliderID', $sliderID)
		->update('slider', [
			'sliderImg' => $file
		]);
	}

	clearGroupCache('sliders');

	endif;
}

public function postSort(){
	if(empty($this->request['list'])):
		$this->validation->addErrors('list', 'data-error');
	endif;

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-sort');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):
		$this->saveSortableList($this->request['list']);
	endif;
}

public function saveSortableList($list, $parent = 0, &$sort = 0) {
    $className = __FUNCTION__;
    foreach($list as $item):
        $sort++;
        $updateAction['sliderSort'] = $sort;
        $this->db
        ->where('sliderID', $item['id'])
        ->update('slider', [
        	'sliderSort' => $sort
        ]);

    clearGroupCache('sliders');
    endforeach;
}

public function __destruct(){
	parent::__destruct();
}


}