<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Authority Filter
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Http\Filters\Package;

use Base\Admin as Admin;
use Illuminate\Support\Str;
use Rewrite;

class Authority extends Admin{

protected $client_url;
protected $access_url;
protected $filter_url;

public function __construct(){
    parent::__construct();
    $this->client_url = option('query-url');
    $this->access_url = array_column(array_merge_recursive($this->getAdminNavigation(), $this->getAdminCategory()), 'url');
    $this->filter_url = Str::contains($this->client_url, config('http.filter.whitelist'));
}

public function filter(){
    if($this->user->data('userID') != 1 && $this->user->is('admin') && !$this->filter_url){
        $client_url = $this->client_url;
        $access_url = $this->access_url;
        $expose_url = array_slice(array_unique(explode('/', $this->client_url)), 4, 2);
        $expose_url = implode($expose_url, '/');
        $client_url = fsprintf('{{url}}/{{control}}/{{path}}', [
            'url' => option('protocol-address'),
            'control' => config('app.system.control'),
            'path' => (!empty($expose_url)) ? $expose_url.'/' : $expose_url,
        ]);

        $response = array_keys(array_filter($access_url, function($var) use ($client_url){
            return Str::contains($var, $client_url) !== false;
        }));

        if(empty($response)){
            $this->pageNotFound();
            exit;
        }
    }
}


}