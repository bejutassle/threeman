<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Base Page Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Base;

use Core\Database;
use Core\Config;
use Core\Template;
use Core\Language;
use Core\Mail;
use Core\Cache;
use Helper\Router;
use Support\Str;
use Supplement\Session as Session;
use Supplement\Validate;
use Supplement\Sms;
use Carbon\Carbon as Time;
use Arrayzy\ArrayImitator as Ary;

use App;
use Request;
use Rewrite;
use Safety;
use Json;

class Page{

protected $settings;
protected $db;
protected $lang;
protected $mail;
protected $cache;
protected $time;
protected $request;
protected $response;
protected $validation;
protected $csrf;
protected $token;
protected $session;
protected $sms;

public function __construct(){
            $date = new \DateTime();
            $request = new Request();
            $this->db = Database::get();
            $this->lang = Language::get();
            $this->mail = Mail::get();
            $this->cache = Cache::get();
            $this->cache->eraseExpired();
            $this->time = Time::instance($date);
            $this->session = new Session();
            $this->csrf = new Safety();
            $this->token = $this->csrf->set(1, 3600);
            $this->request = $request->queryAll();
            $this->response = $request;
            $this->validation = new Validate($this->request);

            if(empty($this->session->get('token'))):
                  $this->session->set('token', $this->token);
            endif;

            date_default_timezone_set(option('time-zone'));
            setlocale(LC_TIME, mb_strtolower(locale_get_display_language(option('locale'), 'en')).'.UTF-8');
            Time::setLocale(option('locale'));
            Template::addPath(rootTemplatePath());
            Template::mainTitle(option('title'));
            Template::append('headerMenu', $this->layoutMenu('header'));
            Template::append('footerMenu', $this->layoutMenu('footer'));
            Template::append('sliders', $this->sliders());
}

public function layoutMenu($type = 'header'){

            $cacheName = "{$type}_menu";
            $this->cache->setCache('global');

            if($this->cache->isCached($cacheName)):
            $layoutMenu = $this->cache->retrieve($cacheName);
            else:
           
            $this->db->where('menuType', $type);
            $this->db->where('menuStatus', 1);
            $this->db->orderBy('menuSort', 'ASC');
            $layoutList = $this->db->get('menu');

              $layoutMenu  = [];
              foreach ($layoutList as $key => $value) {

                  if(!array_key_exists($value['menuID'], $layoutMenu)){
                      $layoutMenu[$value['menuID']]  = $value;
                      if($value['menuLinkType'] == 'category'):
                      $layoutMenu[$value['menuID']]['menuCategory'] = $this->categoryID($value['menuTied']);
                      elseif($value['menuLinkType'] == 'post'):
                      $layoutMenu[$value['menuID']]['menuPost'] = $this->getPosts();
                      elseif($value['menuLinkType'] == 'page'):
                      $layoutMenu[$value['menuID']]['menuPage'] = $this->layoutMenu('header');
                      endif;
                  }else{
                      $layoutMenu[$value['menuID']]  = $value;
                  }

              }

            $this->cache->store($cacheName, $layoutMenu);
            $layoutMenu = $this->cache->retrieve($cacheName);
            endif;

            return $this->layoutMenuChild($layoutMenu);
}

public function sliders($type = 'main'){
            $this->cache->setCache('sliders');

            if($this->cache->isCached('sliders')):
            $sliders = $this->cache->retrieve('sliders');
            else:
            $this->db->where('sliderType', $type);
            $this->db->orderBy('sliderID', 'DESC');
            $sliders = $this->db->get('slider');

            $this->cache->store('sliders', $sliders);
            $sliders = $this->cache->retrieve('sliders');
            endif;

            return $sliders;
}

public function category(){
            $this->cache->setCache('global');

            if($this->cache->isCached('category')):
            $category = $this->cache->retrieve('category');
            else:
            $this->db->where('categoryStatus', 1);
            $this->db->orderBy('categorySort', 'ASC');
            $this->db->groupBy('categoryID');
            $categoryList = $this->db->get('category', NULL, '
                categoryID,
                categoryName,
                categoryParent,
                categorySlug
            ');

                  $category  = [];
                  foreach ($categoryList as $key => $value) {
                      $this->db->where('categoryParent', $value['categoryID']);
                      $subCategory = $this->db->getValue('category', 'categoryID, categoryParent');

                      if(!array_key_exists($value['categoryID'], $category)){
                          $category[$value['categoryID']]  = $value;
                          if(!empty($subCategory)):
                            $category[$value['categoryID']]['subCategory'] = true;
                          else:
                            $category[$value['categoryID']]['subCategory'] = false;
                          endif;
                      }

               }


            $this->cache->store('category', $category);
            $category = $this->cache->retrieve('category');
            endif;

            return $category;
}

public function categoryID($id = 0){
            $getCategoryMenu = $this->category();
            $getCategoryMenu = searchArray($getCategoryMenu, 'categoryParent', $id);
            return $getCategoryMenu;
}

public function layoutMenuChild(array &$elements, $parentID = 0){

    $className = __FUNCTION__;

    $branch = array();    
    foreach ($elements as $element) {
        if ($element['menuParent'] == $parentID) {
            $children = $this->$className($elements, $element['menuID']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[$element['menuID']] = $element;
        }
    }

    return $branch;
}

public function pageNotFound(){
            return Rewrite::get()->requestMap();
}

public function __destruct(){

}


}