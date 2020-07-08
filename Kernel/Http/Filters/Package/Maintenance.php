<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Maintenance Filter
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Http\Filters\Package;

use Base;
use Core\Template as Template;
use Middleware\Base as Middleware;

class Maintenance extends Base{

protected $safe = []; 
protected $user;

public function __construct(){
            parent::__construct();
            $group = array_merge(array_values(config('router.group')), config('http.filter.whitelist'));
            $this->safe = array_diff($group, config('http.filter.blacklist'));
            $this->user = Middleware::get();
}

public function filter(){
        $maintenance = option('maintenance');
        $access = $this->user->is('admin');

        if($maintenance == true && $access == false){
            $url = explode('/', $_SERVER['REQUEST_URI']);
            $status = (in_array($url[1], $this->safe)) ? false : true;

            if($status == true){
                Template::title(trans('title.maintenance'));
                Template::view('error/', 'maintenance');
                exit;
            }
        }
}


}