<?php

namespace Page;

use Base\Page as Page;
use Core\Template as Template;
use Core\Options as Options;
use Support\File as File;
use Support\Str as Str;

class Contact extends Page{

public function __construct(){
    parent::__construct();
}

public function getIndex(){
    Template::title('İletişim');
    Template::widget(['slider' => false, 'submenu' => true]);
    Template::view('main/', 'contact');
}

public function postIndex(){

}

public function __destruct(){
	parent::__destruct();
}


}