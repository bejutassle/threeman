<?php

namespace Admin;

use Base\Admin as Admin;
use Core\Template as Template;
use Arrayzy\ArrayImitator as Ary;

class Home extends Admin{

public function __construct(){
      parent::__construct();
      $this->user->authorized('registered');
}

public function getIndex(){
      Template::title(trans('admin.title.dashboard'));
      Template::append('statics', $this->dataStatics());
      Template::view('main/', 'index');
}

public function dataStatics(){

      $columns = array_keys(trans('admin.statics.column'));
      $query = Ary::create();

      $query->add([
            'name' => 'user', 
            'icon' => 'users',
            'class' => 'primary',
            'count' => $this->db->getValue('users', 'COUNT(userID)'),
      ]);
      $query->add([
            'name' => 'slider', 
            'icon' => 'sliders',
            'class' => 'success',
            'count' => $this->db->getValue('slider', 'COUNT(sliderID)'),
      ]);
      $query->add([
            'name' => 'page', 
            'icon' => 'file-text',
            'class' => 'info',
            'count' => $this->db->getValue('pages', 'COUNT(pageID)'),
      ]);
      $query->add([
            'name' => 'menu', 
            'icon' => 'bars',
            'class' => 'warning',
            'count' => $this->db->getValue('menu', 'COUNT(menuID)'),
      ]);

      return $query->toArray();
}

public function __destruct(){
	parent::__destruct();
}


}