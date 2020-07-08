<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Redirect Filter
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Http\Filters\Package;

class Redirect{

public function filter(){
        $currentURL['env'] = ($_SERVER["SERVER_ADDR"] == '127.0.0.1') ? false : true;
        $currentURL['protocol'] = ($_SERVER["SERVER_PORT"] == 80) ? 'http://' : 'https://';
        $currentURL['address'] = $_SERVER["SERVER_NAME"];
        $query = (!empty($_SERVER['REDIRECT_QUERY_STRING'])) ? $_SERVER['REDIRECT_QUERY_STRING'] : '';
        $redirectAddress = option('protocol').option('address').option('dir').$query;

    if(($currentURL['env'] == true)){

    if(($currentURL['protocol'] != option('protocol')) || ($currentURL['address'] != option('address'))):
                $return = 1;
    else:
                $return = 0;
    endif;

    }else{
                $return = 0;
    }

    if($return == 1){
        return exit(header('Location: '.$redirectAddress));
    }
}


}