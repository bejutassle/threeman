<?php 
/**
 * THREEMAN / Web Application Library
 * @author Emre Emir <emre@emreemir.com>
 * @package Assets Helper
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Helper;

use Rewrite as Rewrite;
use Support\Str as Str;
use Support\File as File;
use Supplement\Session as Session;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\FilterManager;
use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\StringAsset;
use \Assetic\Filter\CssRewrite as CssRewriteFilter;
use \Assetic\Filter\CommentClean as CommentClean;
use \Assetic\Filter\FilterSolid as FilterSolid;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\MinifyCssCompressorFilter;
use Assetic\Filter\CleanCssFilter;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\SeparatorFilter;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\JSMinPlusFilter;
use Assetic\Filter\JSqueezeFilter;
use Assetic\Filter\UglifyJs2Filter;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\Yui;
use Assetic\Cache\FilesystemCache;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Worker\CacheBustingWorker;

class Assets extends FilterSolid{

protected $app;
protected $assets;
protected $tag;
protected $files;
protected $filter;
protected $type;
protected $output;
protected $config;

public function __construct(){
          $this->config = config('cache.assets');
          $this->app = config('app');
          parent::__construct();
}

public function stylesheet($tag, $files = array(), $filter = array(), $output = 'load'){
          $outpath = assets('out/css/');
          $outpathurl = url('css');
          $outcache = $this->config[tplTypeAsset()].'s/';

                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('clean', new CommentClean());
                $fm->set('cssrewrite', new CssRewriteFilter());
                $fm->set('cssmin', new CssMinFilter());
                $fm->set('mincss', new MinifyCssCompressorFilter());

                $factory = new AssetFactory($outpath);
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(false);
                $factory->setDefaultOutput($outpath);
                $factory->addWorker(new CacheBustingWorker());
                if (!is_array($files)) {
                    $files = array_filter(array_map('trim', explode(',', $files)));
                }
                if (!is_array($filter)) {
                    $filter = array_filter(array_map('trim', explode(',', $filter)));
                }

                    $cachePath = new FilesystemCache($outcache);
                    $styles = $factory->createAsset($files, $filter);
                    $cache = new AssetCache($styles, $cachePath);
                    $key = $cache->getHasKey('dump');
                    $name = vsprintf('%s.%s', [$key, 'css']);
                    $tag = ($output == 'dump') ? 
                    str_replace('{{assets_url}}', $cache->dump(), $tag):
                    str_replace('{{assets_url}}', $outpathurl.$name, $tag);
                    if($output == 'load') $cache->dump();
            
          return $tag;
}

public function javascript($tag, $files = array(), $filter = array(), $output = 'load'){
          $outpath = assets('out/js');
          $outpathurl = url('js');
          $outcache = $this->config[tplTypeAsset()].'j/';

                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('clean', new CommentClean());
                $fm->set('minify', new JSMinFilter());
                $fm->set('compress', new JSqueezeFilter());
                $fm->set('seperator', new SeparatorFilter());

                $factory = new AssetFactory($outpath);
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(false);
                $factory->setDefaultOutput($outpath);
                $factory->addWorker(new CacheBustingWorker());
                if (!is_array($files)) {
                    $files = array_filter(array_map('trim', explode(',', $files)));
                }
                if (!is_array($filter)) {
                    $filter = array_filter(array_map('trim', explode(',', $filter)));
                }

                    $cachePath = new FilesystemCache($outcache);
                    $scripts = $factory->createAsset($files, $filter);
                    $cache = new AssetCache($scripts, $cachePath);
                    $key = $cache->getHasKey('dump');
                    $name = vsprintf('%s.%s', [$key, 'js']);

                    $tag = ($output == 'dump') ? 
                    str_replace('{{assets_url}}', $cache->$output(), $tag):
                    str_replace('{{assets_url}}', $outpathurl.$name, $tag);
                    if($output == 'load') $cache->dump();


          return $tag;
}

public function anyIndex($file = NULL){
         $fileinfo = pathinfo($file);
         $rext = ($fileinfo['extension'] == 'css') ? 's' : 'j';
         $output = vsprintf('%s%s/%s', [$this->config[tplTypeAsset()], $rext, $fileinfo['filename']]); //assets("out/{$fileinfo['extension']}/{$file}");
         $mime = getmime($fileinfo['extension']);

        if(!file_exists($output)):
          return Rewrite::get()->requestMap();
        endif;

        //get the last-modified-date of this very file
        $lastModified = filemtime($output);
        //get a unique hash of this file (etag)
        $etagFile = md5_file($output);
        //get the HTTP_IF_MODIFIED_SINCE header if set
        $ifModifiedSince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
        //get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
        $etagHeader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

        //set last-modified header
        header("Content-Type: {$mime}");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
        header("Etag: {$etagFile}");
        header("Cache-Control: public");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822, strtotime(' 1 years')));

        //check if page has changed. If not, send 304 and exit
        if(@strtotime($ifModifiedSince) == $lastModified || $etagHeader == $etagFile){
               header("HTTP/1.1 304 Not Modified");
               exit;
        }

        header("Content-Type: {$mime}");
        //print(file_get_contents($output));
        vprintf('%1$s %2$s', [copyright_ui($this->app['software']), file_get_contents($output)]);
}

}