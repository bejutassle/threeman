<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Image Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use \Rewrite as Rewrite;
use Intervention\Image\ImageManager as ImageManager;
use Intervention\Image\ImageCache as ImageCache;
use Illuminate\Filesystem\Filesystem as FileSystem;
use Illuminate\Cache\FileStore as FileStore;
use Illuminate\Cache\Repository as Repo;

class Image{

protected static $config = array();
protected static $filter = array();
protected static $noimage = array();
protected static $sizes = array();
protected $image;
protected $cache;
protected $filesystem;
protected $filestore;
protected $repository;
const defaultImgPath = STORAGE.'media/img';
const defaultImg = 'no-image.jpg';

public function __construct(){
	self::$config = config('image');	
}

public function anyIndex($path = NULL, $file = NULL, $width = NULL, $height = NULL, $quality = NULL){
		if(empty($file)){
			$file = $path;
			$path = null;
			return $this->getImage($path, $file, $width, $height, $quality);
		}else{
			return $this->getImage($path, $file, $width, $height, $quality);	
		}
}

public function getImage($path = NULL, $file = NULL, $width = NULL, $height = NULL, $quality = 1){
	$this->image = new ImageManager(self::$config);
	$this->filesystem = new Filesystem();
	$this->filestore = new FileStore($this->filesystem, config('cache.image.global'));
	$this->repository = new Repo($this->filestore);
	$this->cache = new ImageCache($this->image, $this->repository);
	$this->cache->lifetime = static::$config['lifetime'];
	self::$filter = ['account', 'mail', 'slide'];
	self::$noimage = ['products'];

	$fileinfo = pathinfo($file);
	$isFilter = ( (!empty($file)) && (in_array($path, self::$filter)) ) ? true : false;
	$isNoImg = ( (!empty($file)) && (in_array($path, self::$noimage)) ) ? true : false;
	$quality = (!empty($isFilter)) ? 0 : 1;

	$source = (!empty($file)) ? 
	vsprintf('%1s/%2s/%3s', [self::defaultImgPath, $path, $file]):
	vsprintf('%1s/%2s', [self::defaultImgPath, $path]);
	$source = preg_replace('/\s+/', '', $source);

	if(file_exists($source)):

	$contentType = mime_content_type($source);
	//get the last-modified-date of this very file
	$lastModified = filemtime($source);
	//get a unique hash of this file (etag)
	$etagFile = md5_file($source);
	//get the HTTP_IF_MODIFIED_SINCE header if set
	$ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
	//get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
	$etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

	//set last-modified header
	header("Content-Type: {$contentType}");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
	header("Etag: $etagFile");
	header('Cache-Control: public');
	header("Pragma: private");
	header("Expires: " . date(DATE_RFC822, strtotime(' 1 years')));

	//check if page has changed. If not, send 304 and exit
	if(@strtotime($ifModifiedSince) == $lastModified || $etagHeader == $etagFile){
	       header("HTTP/1.1 304 Not Modified");
	       exit;
	}

		if($fileinfo['extension'] == 'gif'){

			$source = file_get_contents($source);
			$length = strlen($source);
			header("Content-Length: {$length}");

			print($source);

		}elseif($fileinfo['extension'] == 'svg'){

			$source = file_get_contents($source);
			$length = strlen($source);
			header("Content-Length: {$length}");

			print($source);

		}elseif($fileinfo['extension'] == 'ico'){

			$source = file_get_contents($source);
			$length = strlen($source);
			header("Content-Length: {$length}");

			print($source);

		}else{

	      $img = $this->cache->make($source)->get(static::$config['lifetime'], true);
	      $ext = explode('/', $img->mime())[1];

	      if(!empty($width) && !empty($height)):
	      	$img->fit($width, $height);
	      endif;

	      if($quality == 1):
	      	$img->response($ext, static::$config['quality']);
	      else:
	      	$img->response($ext);
	      endif;

	      print($img);
		}

	elseif(!empty($isNoImg) && (!file_exists($source))):

		return $this->getImage('site', self::defaultImg, $width, $height);

	else:
		return Rewrite::get()->requestMap();
	endif;

}

public function upload($file = 'tmp.jpg', $path = self::defaultImgPath, $name = false){
	$this->image = new ImageManager(config('image'));
	$array = [];

	$uniqid = ($name == false) ? uniqid() : $name.'_'.uniqid();
	$fileinfo = pathinfo($file['name']);
	$fname = vsprintf('%1$s.%2$s', [
		$uniqid, 
		$fileinfo['extension']
	]);
	$array['name'] = $fname;

		if($fileinfo['extension'] == 'gif'){

		$path = vsprintf('%1$s%2$s', [
			$path, 
			$fname
		]);

		move_uploaded_file($file['tmp_name'], $path);


		}elseif($fileinfo['extension'] == 'svg'){

		$path = vsprintf('%1$s%2$s', [
			$path, 
			$fname
		]);

		move_uploaded_file($file['tmp_name'], $path);

		}else{

		$path = vsprintf('%1$s%2$s', [
			$path, 
			$fname
		]);

		$this->image->make($file['tmp_name'])->save($path);

		}

		return $array;
}

public function save($file = NULL, $path = NULL, $size = NULL){
	$this->image = new ImageManager(config('image'));
	$img = $this->image->make($file);

	if(is_array($size)){
	$img->fit($size['width'], $size['height']);
	}

	$img->save($path, 100);
}

}