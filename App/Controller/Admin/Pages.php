<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Support\Str as Str;
use \Json as Json;

class Pages extends Admin{

public function __construct(){
            parent::__construct();
            $this->user->authorized('registered');
}

public function getIndex(){
	  $page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	  $query = (!empty($this->request['q'])) ? $this->request['q'] : '';

	  $pageLimit = 10;
	  $offset = ($page - 1) * $pageLimit;

      $this->db->orderBy('pageDate', 'DESC');
      if(!empty($query)):
    	$this->db->where('pageTitle', '%'.$query.'%', 'LIKE');
      endif;
	  $pages = $this->db->withTotalCount()->get('pages', [$offset, $pageLimit]);
	  $totalItems = $this->db->totalCount;

	  $pageList['totalItems'] = $totalItems;
	  $pageList['totalPages'] = ceil($totalItems / $pageLimit);
	  $pageList['currentPage'] = $page;
	  $pageList['currentFilters'] = url('current', [parseQuery($this->request)]);
	  $pageList['pageSplit'] = '';
	  $pageList['startListPageLimit'] = 1;
	  $pageList['viewPageLimit'] = 1;
	  $pageList['showFirstAndLastPage'] = true;

		Template::title(trans('admin.title.management-page', ['name' => trans('admin.page')]));
		Template::append('pages', $pages);
		Template::append('page', $pageList);
		Template::view('main/page/', 'list');
}

public function getAdd(){
		Template::title(trans('admin.title.add-page', ['name' => trans('admin.page')]));
		Template::view('main/page/', 'add');
}


public function getEdit(){
		$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

		$this->db->where('pageID', $hash);
		$page = $this->db->getOne('pages');

		if(empty($page)):
			return $this->pageNotFound();
		endif;

		Template::title(trans('admin.title.edit-page', ['name' => trans('admin.page')]));
		Template::append('page', $page);
		Template::view('main/page/', 'edit');
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
	->where('pageID', $hash)
	->update('pages', [
		'pageStatus' => 0
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
	->where('pageID', $hash)
	->update('pages', [
		'pageStatus' => 1
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
	->where('pageID', $hash)
	->delete('pages');

	endif;
}

public function postEdit(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->validation->setRules('title', 'required|min:1');
	$this->validation->setRules('content', 'required|min:3');
	$this->validation->setRules('slug', 'required|min:1');

	if($this->db->where('pageSlug', $this->request['slug'])->where('pageID', $hash, '!=')->has('pages')){
	$this->validation->addErrors('slug', 'unique');
	}

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('page');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-update');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db
	->where('pageID', $hash)
	->update('pages', [
		'pageTitle' => $this->request['title'],
		'pageSlug' => $this->request['slug'],
		'pageContent' => $this->request['content'],
		'pageUpdateDate' => $this->db->now(),
		'pageStatus' => $this->request['status'],
	]);

	endif;
}

public function postAdd(){
	$this->response->rule('xhr');

	$this->validation->setRules('title', 'required|min:1');
	$this->validation->setRules('content', 'required|min:3');

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('page');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-insert');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db
	->insert('pages', [
		'pageTitle' => $this->request['title'],
		'pageSlug' => slug($this->request['title'], '-', [
			'table' => 'pages', 
			'column' => 'pageSlug',
		]),
		'pageContent' => $this->request['content'],
		'pageDate' => $this->db->now(),
		'pageStatus' => $this->request['status'],
	]);

	endif;
}

public function __destruct(){
		parent::__destruct();
}


}