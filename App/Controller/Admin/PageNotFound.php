<?php 

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;

class PageNotFound extends Admin{

public function __construct(){
            parent::__construct();
            $this->user->authorized('registered');
}

public function index(){
            Template::title(trans('admin.page-not-found'));
            Template::view('error/', '404');
}

public function __destruct(){
			parent::__destruct();
}


}