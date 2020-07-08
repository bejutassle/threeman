<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Base Admin Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Base;

use Core\Config;
use Core\Database;
use Core\Template;
use Core\Language;
use Core\Mail;
use Core\Image;
use Core\Cache;
use Helper\Router;
use Support\Str;
use Supplement\Session;
use Supplement\Validate;
use Middleware\Base as Middleware;
use Carbon\Carbon as Time;
use Arrayzy\ArrayImitator as Ary;

use App;
use Request;
use Rewrite;
use Settings;
use Safety;
use Json;

class Admin{

protected $settings;
protected $db;
protected $lang;
protected $mail;
protected $cache;
protected $time;
protected $request;
protected $response;
protected $validation;
protected $image;
protected $user;
protected $csrf;
protected $token;
protected $session;

public function __construct(){
            $date = new \DateTime();
            $request = new Request();
            $this->db = Database::get();
            $this->lang = Language::get();
            $this->mail = Mail::get();
            $this->user = Middleware::get();
            $this->cache = Cache::get();
            $this->cache->eraseExpired();
            $this->time = Time::instance($date);
            $this->session = new Session();
            $this->csrf = new Safety();
            $this->image = new Image();
            $this->token = $this->csrf->set(1, 3600);
            $this->request = $request->queryAll();
            $this->response = $request;
            $this->validation = new Validate($this->request);

            if(empty($this->session->get('token'))):
                  $this->session->set('token', $this->token);
            endif;

            $adminCategory = $this->getAdminCategory();
            $adminCategoryChild = $this->getAdminChildCategory();
            $activeMenuID = (!empty($_COOKIE['activeMenuID'])) ? $_COOKIE['activeMenuID'] : 0;
            $breadcrumbs = $this->breadcrumbSort($adminCategory, $activeMenuID);

            date_default_timezone_set(option('time-zone'));
            setlocale(LC_TIME, mb_strtolower(locale_get_display_language(option('locale'), 'en')).'.UTF-8');
            Time::setLocale(option('locale'));
            Template::addPath(rootTemplatePath());
            Template::mainTitle(trans('admin.control-panel'));
            Template::append('sortNavigation', $this->getAdminNavigation());
            Template::append('adminCategory', $adminCategory);
            Template::append('adminCategoryChild', $adminCategoryChild);
            Template::append('breadcrumbs', $breadcrumbs);
}

public function setAdminNavigation($id = 0, $parent = 0){
            $sideMenu = Ary::create();

            //Settings
            $sideMenu->add([
                  'id' => 1, 
                  'name' => trans('admin.sidebar.settings.title'), 
                  'icon' => 'wrench', 
                  'url' => url('settings'),
                  'parent' => 0,
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 2, 
                  'name' => trans('admin.sidebar.settings.webapp'), 
                  'icon' => 'cog', 
                  'url' => url('settings'), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 3, 
                  'name' => trans('admin.sidebar.settings.seo'), 
                  'icon' => 'area-chart', 
                  'url' => url('settings', ['seo']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 4, 
                  'name' => trans('admin.sidebar.settings.google'), 
                  'icon' => 'google', 
                  'url' => url('settings', ['google']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 5, 
                  'name' => trans('admin.sidebar.settings.mail'), 
                  'icon' => 'envelope', 
                  'url' => url('settings', ['mail']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 6, 
                  'name' => trans('admin.sidebar.settings.contact'), 
                  'icon' => 'reply', 
                  'url' => url('settings', ['contact']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 8, 
                  'name' => trans('admin.sidebar.settings.cache'), 
                  'icon' => 'folder', 
                  'url' => url('settings', ['cache']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 9, 
                  'name' => trans('admin.sidebar.settings.maintenance'), 
                  'icon' => 'stethoscope', 
                  'url' => url('settings', ['maintenance']), 
                  'parent' => 1, 
                  'status' => 1
            ]);

            return $sideMenu->toArray();
}

public function getAdminNavigation(){
            if($this->user->data('userID') != 1):
              $categories = $this->setAdminNavigation();
              $access = array_map('intval', explode(',', $this->user->group('userGroupAccess')));
              $adminCategory = array_filter($categories, function ($categories) use ($access) {
                  if(in_array($categories['id'], $access)){
                        return true;
                  }else{
                        return false;
                  }
              });
            else:
              $adminCategory = $this->setAdminNavigation();
            endif;

            return $adminCategory;
}

public function getAdminChildNavigation(){
      $items = array();
      $count = 0;
            foreach ($this->getAdminNavigation() as $child) {
                  if($child['id'] = $child['parent']):
                        $val = $child['id'];
                        $items[$count++] = $val; 
                  endif;
            }

            return $items;

}

public function setAdminCategory($id = 0, $parent = 0){
            $sideMenu = Ary::create();

            //Users
            $sideMenu->add([
                  'id' => 68, 
                  'name' => trans('admin.sidebar.user.title'), 
                  'icon' => 'user', 
                  'url' => url('user'), 
                  'parent' => 0, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 69, 
                  'name' => trans('admin.sidebar.user.add'), 
                  'icon' => 'plus', 
                  'url' => url('user', ['addAction']), 
                  'parent' => 68, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 70, 
                  'name' => trans('admin.sidebar.user.list'), 
                  'icon' => 'list', 
                  'url' => url('user'), 
                  'parent' => 68, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 71, 
                  'name' => trans('admin.sidebar.user.group.title'), 
                  'icon' => 'users', 
                  'url' => url('user', ['group']), 
                  'parent' => 68, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 72, 
                  'name' => trans('admin.sidebar.user.group.add'), 
                  'icon' => 'plus', 
                  'url' => url('user', ['addGroupAction']), 
                  'parent' => 71, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 73, 
                  'name' => trans('admin.sidebar.user.group.list'), 
                  'icon' => 'list', 
                  'url' => url('user', ['group']), 
                  'parent' => 71, 
                  'status' => 1
            ]);

            //Pages
            $sideMenu->add([
                  'id' => 74, 
                  'name' => trans('admin.sidebar.page.title'), 
                  'icon' => 'page', 
                  'url' => url('page'), 
                  'parent' => 0, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 75, 
                  'name' => trans('admin.sidebar.page.add'), 
                  'icon' => 'plus', 
                  'url' => url('page', ['add']), 
                  'parent' => 74, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 76, 
                  'name' => trans('admin.sidebar.page.list'), 
                  'icon' => 'list', 
                  'url' => url('page'), 
                  'parent' => 74, 
                  'status' => 1
            ]);

            //Sliders
            $sideMenu->add([
                  'id' => 77, 
                  'name' => trans('admin.sidebar.slider.title'), 
                  'icon' => 'sliders', 
                  'url' => url('sliders'), 
                  'parent' => 0, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 78, 
                  'name' => trans('admin.sidebar.slider.add'), 
                  'icon' => 'plus', 
                  'url' => url('sliders', ['add']), 
                  'parent' => 77, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 79, 
                  'name' => trans('admin.sidebar.slider.list'), 
                  'icon' => 'sliders', 
                  'url' => url('sliders'), 
                  'parent' => 77, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 80, 
                  'name' => trans('admin.sidebar.slider.sort'), 
                  'icon' => 'sort', 
                  'url' => url('sliders', ['sort']), 
                  'parent' => 77, 
                  'status' => 1
            ]);

            //Menu
            $sideMenu->add([
                  'id' => 81, 
                  'name' => trans('admin.sidebar.menu.title'), 
                  'icon' => 'th-list', 
                  'url' => url('menu'), 
                  'parent' => 0, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 82, 
                  'name' => trans('admin.sidebar.menu.add'), 
                  'icon' => 'plus', 
                  'url' => url('menu', ['add']), 
                  'parent' => 81, 
                  'status' => 1
            ]);

            $sideMenu->add([
                  'id' => 83, 
                  'name' => trans('admin.sidebar.menu.list'), 
                  'icon' => 'list', 
                  'url' => url('menu'), 
                  'parent' => 81, 
                  'status' => 1
            ]);


            $sideMenu->add([
                  'id' => 84, 
                  'name' => trans('admin.sidebar.menu.sort'), 
                  'icon' => 'sort', 
                  'url' => url('menu', ['sort']), 
                  'parent' => 81, 
                  'status' => 1
            ]);


            return $sideMenu->toArray();
}

public function getAdminCategory(){
            if($this->user->data('userID') != 1):
              $categories = $this->setAdminCategory();
              $access = array_map('intval', explode(',', $this->user->group('userGroupAccess')));
              $adminCategory = array_filter($categories, function ($categories) use ($access) {
                  if(in_array($categories['id'], $access)){
                        return true;
                  }else{
                        return false;
                  }
              });
            else:
              $adminCategory = $this->setAdminCategory();
            endif;

            return $adminCategory;
}

public function getAdminChildCategory(){
      $items = array();
      $count = 0;
            foreach ($this->getAdminCategory() as $child) {
                  if($child['id'] = $child['parent']):
                        $val = $child['id'];
                        $items[$count++] = $val; 
                  endif;
            }

            return $items;

}

public function breadcrumbSort(array $categories = array(), $parent = 0){

    $result = [];
    foreach ($categories as $category) {
        if ($parent == $category['id']) {
            if ($category['parent'] > 0) {
                $result = $this->breadcrumbSort($categories, $category['parent']);
            }

            array_push($result, [
                  'name' => $category['name'], 
                  'url' => $category['url'],
                  'icon' => $category['icon']
            ]);

        }
    }
    return $result;
}

public function pageNotFound(){
            return Rewrite::get()->requestMap();
}

public function __destruct(){
}


}