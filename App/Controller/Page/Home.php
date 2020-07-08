<?php

namespace Page;

use Base\Page as Page;
use Core\Template as Template;
use Core\Options as Options;
use Support\File as File;
use Support\Str as Str;

class Home extends Page{

public function __construct(){
    parent::__construct();
    //$this->user->authorized('registered');
}

public function anyIndex($slug = NULL){
	return (!empty($slug)) ? $this->contentPage($slug) : $this->indexPage();
}

public function indexPage(){
    Template::title('');
    Template::widget(['slider' => true, 'submenu' => true]);
    Template::view('main/', 'index');
}

public function contentPage($slug){
	$page = $this->db
	->where('pageSlug', $slug)
	->getOne('pages');

	if(empty($page)):
		return $this->pageNotFound();
	endif;

    Template::title($page['pageTitle']);
    Template::widget(['slider' => false, 'submenu' => true]);
    Template::append('page', $page);
    Template::view('main/', 'content');
}

public function __destruct(){
	parent::__destruct();
}


}