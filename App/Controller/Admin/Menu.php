<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Support\Str as Str;
use \Json as Json;

class Menu extends Admin{

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

	$where['col'] = (count($where) == 2) ? array_shift($where) : 'menuID';
	$where['var'] = (count($where) == 2) ? array_shift($where) : 0;
	$where['op'] = ($where['col'] == 'menuID') ? '!=' : '=';
	$filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'menuDate';
	$filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'ASC';

	if(!empty($query)):
	$this->db->where('menuName', '%'.$query.'%', 'LIKE');
	endif;
	$this->db->where($where['col'], $where['var'], $where['op']);
	$this->db->orderBy($filter['col'], $filter['var']);
	$this->db->orderBy('menuParent', 'ASC');
	$menu = $this->db->withTotalCount()->get('menu', [($page - 1) * $limit, $limit]);
	$totalItems = $this->db->totalCount;

	$pageList['totalItems'] = $totalItems;
	$pageList['totalPages'] = ceil($totalItems / $limit);
	$pageList['currentPage'] = $page;
	$pageList['currentFilters'] = url('current', [parseQuery($this->request)]);
	$pageList['pageSplit'] = '';
	$pageList['startListPageLimit'] = 1;
	$pageList['viewPageLimit'] = 1;
	$pageList['showFirstAndLastPage'] = true;

	$menuList  = [];
	foreach ($menu as $key => $value) {
		$menuParentName = ($where['var'] == 'category') ? $this->menuParentName($value['menuParent']) : $this->menuParentName($value['menuParent']);
	    if(!array_key_exists($value['menuID'], $menuList)){
	        $menuList[$value['menuID']]  = $value;
	        $menuList[$value['menuID']]['menuParentName'] = $menuParentName;
	    }else{
	        $menuList[$value['menuID']]  = $value;
	        $menuList[$value['menuID']]['menuParentName'] = $menuParentName;
	    }
	}

	$menuTypes = ['header', 'footer', 'left', 'right', 'middle', 'category'];

	Template::title(trans('admin.title.management-page', ['name' => trans('admin.menu')]));
	Template::append('menus', $menuList);
	Template::append('page', $pageList);
	Template::append('menuTypes', $menuTypes);
	Template::view('main/menu/', 'list');
}

public function getSort(){
	$page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	$query = (!empty($this->request['q'])) ? $this->request['q'] : '';
	$filter = (!empty($this->request['f'])) ? explode(',', $this->request['f']) : [];
	$where = (!empty($this->request['w'])) ? array_map('html_entity_decode', explode(',', $this->request['w'])) : [];
	$limit = (!empty($this->request['l'])) ? ($this->request['l'] == 'all') ? 9999999 : $this->request['l'] : 10;
	$all = (!empty($this->request['a'])) ? explode(',', $this->request['a']) : [];

	$where['col'] = (count($where) == 2) ? array_shift($where) : 'menuID';
	$where['var'] = (count($where) == 2) ? array_shift($where) : NULL;
	$where['op'] = ($where['col'] == 'menuID') ? '!=' : '=';
	$filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'menuSort';
	$filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'ASC';

	$this->db->where($where['col'], $where['var'], $where['op']);
	$this->db->orderBy($filter['col'], $filter['var']);
	$menuList = $this->db->get('menu', NULL, 'menuID, menuName, menuParent');
	$menuChildList = $this->childMenu($menuList);
	$menuTypes = ['header', 'footer', 'left', 'right', 'middle', 'category'];

	Template::title(trans('admin.title.sort-page', ['name' => trans('admin.menu')]));
	Template::append('menuVar', (string) $where['var']);
	Template::append('menus', $menuList);
	Template::append('menuChild', $menuChildList);
	Template::append('menuTypes', $menuTypes);
	Template::view('main/menu/', 'sort');
}

public function getAdd(){
	$menuParent = $this->db
	->where('menuParent', 0)
	->get('menu');

	$type = ['footer', 'header', 'left', 'right', 'middle', 'category'];
    $target = ['_self', '_blank', '_parent', '_top'];
	$link = ['links', 'social', 'category', 'contact', 'image', 'page', 'post'];


	Template::title(trans('admin.title.add-page', ['name' => trans('admin.menu')]));
	Template::append('types', $type);
	Template::append('parents', $menuParent);
	Template::append('targets', $target);
	Template::append('links', $link);
	Template::view('main/menu/', 'add');
}


public function getEdit(){
   $hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

   $this->db->where('menuID', $hash);
   $menu = $this->db->getOne('menu');

   $this->db->where('menuParent', 0);
   $this->db->where('menuType', $menu['menuType']);
   $menuParent = $this->db->get('menu');

   $type = ['footer', 'header', 'left', 'right', 'middle', 'category'];
   $target = ['_self', '_blank', '_parent', '_top'];
   $link = ['links', 'social', 'category', 'contact', 'image', 'page', 'post'];

   if(empty($menu)):
   	return $this->pageNotFound();
   endif;

	Template::title(trans('admin.title.edit-page', ['name' => trans('admin.category')]));
	Template::append('menu', $menu);
	Template::append('types', $type);
	Template::append('parents', $menuParent);
	Template::append('targets', $target);
	Template::append('links', $link);
	Template::view('main/menu/', 'edit');
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
	->where('menuID', $hash)
	->update('menu', [
		'menuStatus' => 0
	]);

	$this->clearCache(0, $hash);

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
	->where('menuID', $hash)
	->update('menu', [
		'menuStatus' => 1
	]);

	$this->clearCache(0, $hash);

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
	->where('menuID', $hash)
	->delete('menu');

	$this->clearCache(0, $hash);

	endif;
}

public function postEdit(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->request['show-type']['guest'] = ($this->request['show-type']['guest'] == 'on') ? 1 : 0;
	$this->request['show-type']['user'] = ($this->request['show-type']['user'] == 'on') ? 1 : 0;

	$this->validation->setRules('name', 'required|min:3');
	$this->validation->setRules('type', 'required');
	$this->validation->setRules('parent', 'required');

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
	->where('menuID', $hash)
	->update('menu', [
		'menuType' => $this->request['type'],
		'menuParent' => $this->request['parent'],
		'menuName' => $this->request['name'],
		'menuTitle' => $this->request['title'],
		'menuLink' => $this->request['link'],
		'menuLinkType' => $this->request['link_type'],
		'menuLinkTarget' => $this->request['target'],
		'menuIcon' => $this->request['icon'],
		'menuClass' => $this->request['class'],
		'menuShowUser' => $this->request['show-type']['user'],
		'menuShowGuest' => $this->request['show-type']['guest'],
		'menuUpdateDate' => $this->db->now(),
		'menuStatus' => $this->request['status'],
	]);

	$this->clearCache($this->request['type']);

	endif;
}

public function postAdd(){
	$this->response->rule('xhr');

	$this->request['show-type']['guest'] = ($this->request['show-type']['guest'] == 'on') ? 1 : 0;
	$this->request['show-type']['user'] = ($this->request['show-type']['user'] == 'on') ? 1 : 0;

	$this->validation->setRules('name', 'required|min:3');
	$this->validation->setRules('type', 'required');
	$this->validation->setRules('parent', 'required');

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('menu');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-insert');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$this->db->insert('menu', [
		'menuType' => $this->request['type'],
		'menuParent' => $this->request['parent'],
		'menuName' => $this->request['name'],
		'menuTitle' => $this->request['title'],
		'menuLink' => $this->request['link'],
		'menuLinkType' => $this->request['link_type'],
		'menuLinkTarget' => $this->request['target'],
		'menuIcon' => $this->request['icon'],
		'menuClass' => $this->request['class'],
		'menuShowUser' => $this->request['show-type']['user'],
		'menuShowGuest' => $this->request['show-type']['guest'],
		'menuDate' => $this->db->now(),
		'menuStatus' => $this->request['status'],
	]);

	$this->clearCache($this->request['type']);

	endif;
}

public function getMenutypes(){
	$this->response->rule('xhr');

	$menuType = (!empty($this->request['type'])) ? $this->request['type'] : 0;

	$json['success'] = true;

	if($menuType == 'category'){

		$category = $this->db
		->where('categoryParent', 0)
		->where('categoryStatus', 1)
		->get('category');

			if(!empty($category)):

			$json['results'] = [];

			foreach ($category as $key => $value):
				array_push($json['results'], [
					'menuID' => $value['categoryID'], 
					'menuType' => 'category', 
					'menuName' => $value['categoryName']
				]);
			endforeach;

			else:

			$json['results'] = [];
				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('main')
				]);

			endif;

	}elseif($menuType == 'page'){

			$page = $this->db
			->where('pageStatus', 1)
			->get('pages');

			if(!empty($page)):

			$json['results'] = [];

				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('all')
				]);

			foreach ($page as $key => $value):
				array_push($json['results'], [
					'menuID' => $value['pageID'], 
					'menuType' => 'page', 
					'menuName' => $value['pageTitle']
				]);
			endforeach;

			else:

			$json['results'] = [];
				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('all')
				]);

			endif;

	}elseif($menuType == 'post'){

		$post = $this->db
		->where('postStatus', 1)
		->get('posts');

			if(!empty($post)):

			$json['results'] = [];

				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('all')
				]);

			foreach ($post as $key => $value):
				array_push($json['results'], [
					'menuID' => $value['postID'], 
					'menuType' => 'post', 
					'menuName' => $value['postTitle']
				]);
			endforeach;

			else:

			$json['results'] = [];
				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('all')
				]);

			endif;

	}else{

		$menu = $this->db
		->where('menuType', $menuType)
		->where('menuParent', 0)
		->get('menu');

			if(!empty($menu)):

			$json['results'] = [];

				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('main')
				]);

			foreach ($menu as $key => $value):
				array_push($json['results'], [
					'menuID' => $value['menuID'], 
					'menuType' => $value['menuType'], 
					'menuName' => $value['menuName']
				]);
			endforeach;

			else:

			$json['results'] = [];
				array_push($json['results'], [
					'menuID' => 0, 
					'menuType' => $menuType, 
					'menuName' => trans('main')
				]);

			endif;
	}

	$json = Json::response($json, $this->request, $this->token);

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
		$this->saveSortableList($this->request['type'], $this->request['list']);
	endif;
}

public function saveSortableList($type, $list, $parent = 0, &$sort = 0) {

	$className = __FUNCTION__;

	foreach($list as $item):
	    $sort++;

	    if($type == 'category'){
		    if($parent != 0){
			    $this->db
			    ->where('menuID', $item['id'])
			    ->where('menuType', $type)
			    ->update('menu', [
			    	'menuParent' =>$parent, 
			    	'menuSort' => $sort
			    ]);
		    }
	    }else{
		    $this->db
		    ->where('menuID', $item['id'])
		    ->where('menuType', $type)
		    ->update('menu', [
		    	'menuParent' =>$parent, 
		    	'menuSort' => $sort
		    ]);	
	    }


	    if(array_key_exists('children', $item)) {
	        $this->$className($type, $item['children'], $item['id'], $sort);
	    }

	endforeach;

	$this->clearCache($type);
}

public function menuParentName($parentID = 0){
   $this->db->where('menuID', $parentID);
   $menuData = $this->db->getOne('menu');

   return (!empty($menuData['menuName'])) ? $menuData['menuName'] : trans('main');
}

public function categoryParentName($parentID = 0){
   $this->db->where('categoryID', $parentID);
   $categoryData = $this->db->getOne('category');

	return (!empty($categoryData['categoryName'])) ? $categoryData['categoryName'] : trans('main');
}

public function childMenu($parentMenu = NULL){
	$items = array();
	$count = 0;
	    foreach ($parentMenu as $child) {
	          if($child['menuID'] = $child['menuParent']):
	                $val = $child['menuID'];
	                $items[$count++] = $val;
	          endif;
	    }

	    return $items;
}

private function clearCache($type, $hash = NULL){
	if((!empty($hash)) && (empty($type))){
		$type = $this->db
		->where('menuID', $hash)
		->getValue('menu', 'menuType');
	}

	$this->cache->setCache('global');
	if($this->cache->isCached("{$type}_menu")){
		$this->cache->erase("{$type}_menu");
	}

}

public function __destruct(){
	parent::__destruct();
}


}