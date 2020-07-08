<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Router Helper
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Helper;

use Phroute\Phroute\RouteCollector as Route;
use Phroute\Phroute\Dispatcher as Map;
use Rewrite;
use Support\Str;

class Router{

protected static $settings;

private static $register;
private static $url;
private static $set;
private static $getURL;
const imgPath = 'img';
const defPath = 'img';

public function __construct(){

}

public static function setRegister(){
            return static::$set = true;
}

protected static function getRegister(){
            $register = (static::$set == true) ? '' : Rewrite::requestRegisterGroup();
            $var = (!empty($register)) ? $register : '';
            static::$register = $var;

            if($var == ''):
              static::$url = getURL();
              static::$getURL = getURL();
            else:
              static::$url = sprintf('%s/%s', getURL(), $var);
              static::$getURL = getURL().'/';
            endif;

            return static::$register;
}

public static function url($name = NULL, array $args = NULL){
            static::getRegister();

            if($name == 'http_referer'):

            $return = self::previousURL();

            elseif($name == 'current'):

            $replace = is_null($args) ? [] : array_values($args);
            $var = 0;
            $brackets = '/';

            if(!empty($replace)):

            foreach($args as $part):

            if($var === 0):
            $url[] = $brackets;
            endif;

                $url[] = $replace[$var++];

                $lastChar = substr($part, -1);

            if($lastChar != '='){

                $url[] = $brackets;
            }

            endforeach;

            elseif(empty($name)):

            $url[] = $brackets;

            else:

            $url[] = $brackets;

            endif;

            $full_url = Rewrite::requestCurrent().implode('', $url);
            $parts = explode('//', $full_url, 2);
            $parts[1] = rtrim(preg_replace('@/+@', '/', $parts[1]), '/');
            $full_url = implode('//', $parts);
            preg_match_all("/(\?[a-z+&\$_.-][a-z0-9;:@&%=+\\$.-]*)/", $full_url, $clear_param);
            $clear_param = array_unique(end($clear_param));
            $clear_param = end($clear_param);
            $full_url = strtok($full_url, "?");
            unset($parts);

            $return = $full_url.$clear_param;

            else:

            $stripos = stripos($name, '../');
            if($stripos !== false){
              static::$set = true;
              $name = substr($name, 3);
            }else{
              static::$set = false;
            }
            static::getRegister();
            $replace= is_null($args) ? [] : array_values($args);
            $var = 0;
            $brackets = '/';

            $name = (!empty($name)) ? static::$register.$brackets.$name : $name ;

            if(!empty($replace)):

            foreach($args as $part):

            if($var === 0):
            $url = [$name];
            $url[] = $brackets;
            endif;

                $url[] = $replace[$var++];

                $lastChar = substr($part, -1);

            if($lastChar != '='){

                $url[] = $brackets;
            }

            endforeach;

            elseif(empty($name)):

            $url = [static::$register];
            $url[] = $brackets;

            else:

            $url = [$name];
            $url[] = $brackets;

            endif;

            $return = static::$getURL.implode('', $url);

            endif;

            if(Str::contains($return, config('http.filter.url_files'))){
              $return = rtrim($return, '/');
            }

            return $return;

}

public static function image($img = NULL, $path = self::defPath, $width = NULL, $height = NULL, $quality = NULL){

          $isQuality = ($quality == 1) ? true : false;

          if($isQuality){

            if($path == 'root'):
              if(!empty($width) && !empty($height) && !empty($isQuality)):
                $file = sprintf('%1$s/%2$s/%3$s/%4$s/%5$s/%6$s/0', getURL(), self::imgPath, 'site', $img, $width, $height, $quality);
              elseif(!empty($width) && !empty($height)):
                $file = sprintf('%1$s/%2$s/%3$s/%4$s/%5$s/%6$s', getURL(), self::imgPath, 'site', $img, $width, $height);
              else:
                $file = sprintf('%1$s/%2$s/%3$s/%4$s', getURL(), self::imgPath, 'site', $img);
              endif;
            else:
              if(!empty($width) && !empty($height) && !empty($isQuality)):
                $file = sprintf('%1$s/%2$s/%3$s/%4$s/%5$s/%6$s/0', static::$url, self::imgPath, $path, $img, $width, $height, $quality);
              elseif(!empty($width) && !empty($height)):
                $file = sprintf('%1$s/%2$s/%3$s/%4$s/%5$s/%6$s', static::$url, self::imgPath, $path, $img, $width, $height);
              else:
                $file = sprintf('%1$s/%2$s/%3$s/%4$s', static::$url, self::imgPath, $path, $img);
              endif;
            endif;

          }else{

            $file = fsprintf('{{url}}/{{folder}}/{{path}}/{{file}}', [
              'url' => getURL(),
              'folder' => self::defPath,
              'path' => (!empty($path)) ? $path : '',
              'file' => (!empty($img)) ? $img : '',
            ]);

          }

            $parts = explode('//', $file, 2);
            $parts[1] = rtrim(preg_replace('@/+@', '/', $parts[1]), '/');
            $file = implode('//', $parts);

          return $file;
}

public static function previousURL(){

          if (!empty($_SERVER["HTTP_REFERER"])) {
                $referer = parse_url($_SERVER['HTTP_REFERER']);
                $getURL = rtrim(static::$getURL, '/');

                return vsprintf('%s%s', [
                  $getURL, 
                  $referer['path']
                ]);
                
          }else{
                return self::URL('');
          }
}

protected function requestMethod(){
          return $_SERVER['REQUEST_METHOD'];
}

protected function requestUri(){
          return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

public function __destruct(){

}

}