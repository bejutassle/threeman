<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Core\Mail as Mail;
use Helper\Router as Router;
use Helper\BBCode as BBCode;
use Support\Str as Str;
use \Json as Json;

class User extends Admin{

public function __construct(){
            parent::__construct();
            $this->user->authorized('registered');
}

public function anyIndex($slug){
			$class = get_called_class();
			return (method_exists($class, $slug)) ? $this->$slug() : $this->pageNotFound();
}

public function getIndex(){
	  $page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	  $query = (!empty($this->request['q'])) ? html_entity_decode($this->request['q']) : '';
	  $filter = (!empty($this->request['f'])) ? explode(',', $this->request['f']) : [];
	  $where = (!empty($this->request['w'])) ? array_map('html_entity_decode', explode(',', $this->request['w'])) : [];
	  $all = (!empty($this->request['a'])) ? explode(',', $this->request['a']) : [];

	  $where['col'] = (count($where) == 2) ? array_shift($where) : 'userID';
	  $where['var'] = (count($where) == 2) ? array_shift($where) : 0;
	  $where['op'] = ($where['col'] == 'userID') ? '!=' : '=';
	  $filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'userRegisterDate';
	  $filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'DESC';

	  $pageLimit = 10;
	  $offset = ($page - 1) * $pageLimit;


      if(!empty($query)):
    	$this->db->where('CONCAT(userFirstName," ",userLastName)', '%'.$query.'%', 'LIKE');
    	$this->db->orWhere('userEmail', '%'.$query.'%', 'LIKE');
    	$this->db->orWhere('userName', '%'.$query.'%', 'LIKE');
      endif;
	  $this->db->where($where['col'], $where['var'], $where['op']);
	  $this->db->orderBy($filter['col'], $filter['var']);
	  $users = $this->db->withTotalCount()->get('users', (!empty($all)) ? NULL : [$offset, $pageLimit]);
	  $totalItems = $this->db->totalCount;

	  $pageList['totalItems'] = $totalItems;
	  $pageList['totalPages'] = ceil($totalItems / $pageLimit);
	  $pageList['currentPage'] = $page;
	  $pageList['currentFilters'] = url('current', [parseQuery($this->request)]);
	  $pageList['pageSplit'] = '';
	  $pageList['startListPageLimit'] = 1;
	  $pageList['viewPageLimit'] = 1;
	  $pageList['showFirstAndLastPage'] = true;
	  if(!empty($all)) $pageList = NULL;

	  $groups = $this->db->get('users_group', NULL, 'userGroupID, userGroupName');

	  Template::title(trans('admin.title.management-page', ['name' => trans('admin.user')]));
	  Template::append('users', $users);
	  Template::append('groups', $groups);
	  Template::append('page', $pageList);
      Template::view('main/user/', 'list');
}

public function addAction(){
	  $userGroup = $this->db->get('users_group', NULL, 'userGroupID, userGroupName');

	  Template::title(trans('admin.title.add-page', ['name' => trans('admin.user')]));
	  Template::append('userGroup', $userGroup);
      Template::view('main/user/', 'add');
}

public function editAction(){
	   $hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	   $this->db->where('userID', $hash);
	   $user = $this->db->getOne('users');
	   $userGroup = $this->db->get('users_group', NULL, 'userGroupID, userGroupName');

	   if(empty($user)):
	   	return $this->pageNotFound();
	   endif;

	  Template::title(trans('admin.title.edit-page', ['name' => trans('admin.user')]));
	  Template::append('user', $user);
	  Template::append('userGroup', $userGroup);
      Template::view('main/user/', 'edit');
}

public function activityAction(){
      $hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

      $activitys = $this->db
      ->where('userActivityUserID', $hash)
      ->where('userActivityGroup', 0)
      ->get('users_activity', NULL, 'userActivityType, userActivityDate, INET_NTOA(userActivityIP) AS userActivityIP');

      Template::append('activitys', $activitys);
      Template::view('main/user/modal/', 'activity');
}

public function passiveAction(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($hash == 1):
		$this->validation->addErrors('error', trans('information.data-operation'));
	endif;

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

	$updateAction['userStatus'] = 0;

	$this->db->where('userID', $hash);
	$this->db->update('users', $updateAction);

	endif;
}

public function activateAction(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($hash == 1):
		$this->validation->addErrors('error', trans('information.data-operation'));
	endif;

	if(!empty($rules)):

	$json['success'] = false;
	$json['message'] = $rules;

	else:

	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-activate');

	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$updateAction['userStatus'] = 1;

	$this->db->where('userID', $hash);
	$this->db->update('users', $updateAction);

	endif;
}

public function deleteAction(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if($hash == 1):
		$this->validation->addErrors('error', trans('information.data-operation'));
	endif;

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

	$this->db->where('userID', $hash);
	$this->db->delete('users');

	endif;
}

public function updateAction(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->validation->setRules('username', 'required');
	$this->validation->setRules('first_name', 'required|min:1');
	$this->validation->setRules('last_name', 'required|min:1');
	$this->validation->setRules('email', 'required|email');
	$this->validation->setRules('group', 'required_if:mod,1');

	if(strlen($this->request['password']) > 3):
		$updatePassword = true;
	else:
		$updatePassword = true;
	endif;

	$this->db->where('userID', $hash, '!=');
	$this->db->where('userEmail', $this->request['email']);
	if($this->db->has('users')):
		$this->validation->addErrors('email', 'unique');
	endif;

	$this->db->where('userID', $hash, '!=');
	$this->db->where('userName', $this->request['username']);
	if($this->db->has('users')):
		$this->validation->addErrors('username', 'unique');
	endif;


	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-update');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$updateAction['userName'] = $this->request['username'];
	$updateAction['userFirstName'] = $this->request['first_name'];
	$updateAction['userLastName'] = $this->request['last_name'];
	if($this->request['password'] != '' && $updatePassword == true) $updateAction['userPassword'] = sha1($this->request['password']);
	$updateAction['userEmail'] = $this->request['email'];
	if($hash != 1):
	$updateAction['userMod'] = $this->request['mod'];
	if(!empty($this->request['mod'])):
	$updateAction['userGroup'] = $this->request['group'];
	endif;
	$updateAction['userUpdateDate'] = $this->db->now();
	$updateAction['userStatus'] = $this->request['status'];
	endif;

	$this->db->where('userID', $hash);
	$this->db->update('users', $updateAction);

	endif;
}

public function insertAction(){
	$this->response->rule('xhr');

	$this->validation->setRules('username', 'required');
	$this->validation->setRules('first_name', 'required|min:1');
	$this->validation->setRules('last_name', 'required|min:1');
	$this->validation->setRules('email', 'required|email');
	$this->validation->setRules('password', 'required|min:3');
	$this->validation->setRules('group', 'required_if:mod,1');

	$this->db->where('userEmail', $this->request['email']);
	if($this->db->has('users')):
		$this->validation->addErrors('email', 'unique');
	endif;

	$this->db->where('userName', $this->request['username']);
	if($this->db->has('users')):
		$this->validation->addErrors('username', 'unique');
	endif;


	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('user');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-insert');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$insertAction['userName'] = $this->request['username'];
	$insertAction['userFirstName'] = $this->request['first_name'];
	$insertAction['userLastName'] = $this->request['last_name'];
	$insertAction['userPassword'] = sha1($this->request['password']);
	$insertAction['userEmail'] = $this->request['email'];
	$insertAction['userMod'] = $this->request['mod'];
	if(!empty($this->request['mod'])):
	$insertAction['userGroup'] = $this->request['group'];
	endif;
	$insertAction['userLastLogin'] = $this->db->now();
	$insertAction['userRegisterDate'] = $this->db->now();
	$insertAction['userStatus'] = $this->request['status'];

	$this->db->insert('users', $insertAction);

	endif;
}

public function group(){
	  $page = (!empty($this->request['p'])) ? $this->request['p'] : 1;
	  $query = (!empty($this->request['q'])) ? html_entity_decode($this->request['q']) : '';
	  $filter = (!empty($this->request['f'])) ? explode(',', $this->request['f']) : [];
	  $where = (!empty($this->request['w'])) ? array_map('html_entity_decode', explode(',', $this->request['w'])) : [];
	  $all = (!empty($this->request['a'])) ? explode(',', $this->request['a']) : [];

	  $where['col'] = (count($where) == 2) ? array_shift($where) : 'userGroupID';
	  $where['var'] = (count($where) == 2) ? array_shift($where) : 0;
	  $where['op'] = ($where['col'] == 'userGroupID') ? '!=' : '=';
	  $filter['col'] = (count($filter) == 2) ? array_shift($filter) : 'userGroupDate';
	  $filter['var'] = (count($filter) == 2) ? array_shift($filter) : 'DESC';

	  $pageLimit = 10;
	  $offset = ($page - 1) * $pageLimit;


      if(!empty($query)):
    	$this->db->orWhere('userGroupName', '%'.$query.'%', 'LIKE');
      endif;
	  $this->db->where($where['col'], $where['var'], $where['op']);
	  $this->db->orderBy($filter['col'], $filter['var']);
	  $groups = $this->db->withTotalCount()->get('users_group', (!empty($all)) ? NULL : [$offset, $pageLimit]);
	  $totalItems = $this->db->totalCount;

	  $pageList['totalItems'] = $totalItems;
	  $pageList['totalPages'] = ceil($totalItems / $pageLimit);
	  $pageList['currentPage'] = $page;
	  $pageList['currentFilters'] = url('current', [parseQuery($this->request)]);
	  $pageList['pageSplit'] = '';
	  $pageList['startListPageLimit'] = 1;
	  $pageList['viewPageLimit'] = 1;
	  $pageList['showFirstAndLastPage'] = true;
	  if(!empty($all)) $pageList = NULL;

	  Template::title(trans('admin.title.management-page', ['name' => trans('admin.user-groups')]));
	  Template::append('groups', $groups);
	  Template::append('page', $pageList);
      Template::view('main/user/group/', 'list');
}

public function addGroupAction(){
	   $groupAccess = array_merge_recursive($this->setAdminNavigation(), $this->setAdminCategory());
	   $groupChildAccess = array_merge_recursive($this->getAdminChildNavigation(), $this->getAdminChildCategory());

	   Template::title(trans('admin.title.add-page', ['name' => trans('admin.user-groups')]));
	   Template::append('groupAccess', $groupAccess);
	   Template::append('groupChildAccess', $groupChildAccess);
	   Template::view('main/user/group/', 'add');
}

public function editGroupAction(){
	   $hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	   $this->db->where('userGroupID', $hash);
	   $group = $this->db->getOne('users_group');
	   $groupAccess = array_merge_recursive($this->setAdminNavigation(), $this->setAdminCategory());
	   $groupChildAccess = array_merge_recursive($this->getAdminChildNavigation(), $this->getAdminChildCategory());

	   if(empty($group)):
	   	return $this->pageNotFound();
	   endif;

	  Template::title(trans('admin.title.edit-page', ['name' => trans('admin.user-groups')]));
	  Template::append('group', $group);
	  Template::append('groupAccess', $groupAccess);
	  Template::append('groupChildAccess', $groupChildAccess);
      Template::view('main/user/group/', 'edit');
}

public function passiveGroupAction(){
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

	$updateAction['userStatus'] = 0;

	$this->db->where('userGroupID', $hash);
	$this->db->update('users_group', $updateAction);

	endif;
}

public function activateGroupAction(){
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	if(!empty($rules)):

	$json['success'] = false;
	$json['message'] = $rules;

	else:

	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-activate');

	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$updateAction['userStatus'] = 1;

	$this->db->where('userGroupID', $hash);
	$this->db->update('users_group', $updateAction);

	endif;
}

public function deleteGroupAction(){
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

	$this->db->where('userGroupID', $hash);
	$this->db->delete('users_group');

	endif;
}

public function updateGroupAction(){
	$this->response->rule('xhr');
	$hash = (!empty($this->request['hash'])) ? decrypt($this->request['hash']) : 0;

	$this->validation->setRules('name', 'required|min:1');
	$this->validation->setRules('access', 'required');

	$this->db->where('userGroupID', $hash, '!=');
	$this->db->where('userGroupName', $this->request['name']);
	if($this->db->has('users_group')):
		$this->validation->addErrors('name', 'unique');
	endif;


	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('');
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-update');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$updateAction['userGroupName'] = $this->request['name'];
	$updateAction['userGroupAccess'] = (!empty($this->request['access'])) ? implode(',', $this->request['access']) : NULL;
	$updateAction['userGroupUpdateDate'] = $this->db->now();
	$updateAction['userGroupStatus'] = $this->request['status'];

	$this->db->where('userGroupID', $hash);
	$this->db->update('users_group', $updateAction);

	endif;
}

public function insertGroupAction(){
	$this->response->rule('xhr');

	$this->validation->setRules('name', 'required|min:1');
	$this->validation->setRules('access', 'required');

	$this->db->where('userGroupName', $this->request['name']);
	if($this->db->has('users_group')):
		$this->validation->addErrors('name', 'unique');
	endif;

	if($this->validation->errors()):
	$json['success'] = false;
	$json['message'] = $this->validation->errors();
	else:
	$json['success'] = true;
	$json['url'] = url('user', ['group']);
	$json['message'] = trans('information.successful!');
	$json['description'] = trans('information.successful-insert');
	endif;

	$json = Json::response($json, $this->request, $this->token);

	if(Json::message() == true):

	$insertAction['userGroupName'] = $this->request['name'];
	$insertAction['userGroupAccess'] = (!empty($this->request['access'])) ? implode(',', $this->request['access']) : NULL;
	$insertAction['userGroupDate'] = $this->db->now();
	$insertAction['userGroupStatus'] = $this->request['status'];

	$this->db->insert('users_group', $insertAction);

	endif;
}

public function __destruct(){
			parent::__destruct();
}


}