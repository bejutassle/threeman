<?php 

namespace Page;

use Base\Page as Page;
use Core\Template as Template;

class PageNotFound extends Page{

public function __construct(){
            parent::__construct();
}

public function index(){
            Template::title(trans('title.page-not-found'));
            Template::view('error/', '404');
}

public function __destruct(){
			parent::__destruct();
}


}