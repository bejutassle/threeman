<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Json Response
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use \Core\Config as Config;
use \Safety as Safety;
use \Supplement\Session as Session;
use \Supplement\Google as Google;

class Json{

protected static $Instance = array();
protected static $message;

public static function get() {
            $class = get_called_class();
            if(! isset(static::$Instance[$class])):
                  static::$Instance[$class] = new $class();
            endif;
            return static::$Instance[$class];
}

public function __construct(){

}

public static function response($message = NULL, $request = NULL, $token = NULL){

if( (!empty($request)) && (!empty($token)) ):
    $security = new Safety();
    $session = new Session();

    if($request['token'] !== $session->get('token')):
        unset($message);
            $message['message']['token'] = true;
            $message['message']['token_title'] = trans('information.token_title');
            $message['message']['token_message'] = trans('information.token_message');
            $message['message']['token_button'] = trans('information.token_button');
            $message['success'] = false;
            $security->delete($token);
    endif;

endif;

if( (config('google.recaptcha.status') == 'true') && !empty($request['recaptcha']) && ($request['recaptcha'] == 1) ):
    $google = new Google();
    $recaptcha = $google->validateRecaptcha($request['g-recaptcha-response']);

    if(!$recaptcha->success):
            if(!is_array($message['message'])):
            unset($message['message']);
            unset($message['description']);
            endif;
            $message['message']['recaptcha'] = trans('information.recaptcha-error');
            $message['success'] = false;
    endif;

endif;

    if( (!empty($message['success'])) && ($message['success'] == true) ):
            $code = 200;
            static::$message = true;
    else:
            $code = 422;
            static::$message = false;
    endif;

    header_remove();
    http_response_code($code);
    $http_origin = $_SERVER['HTTP_HOST'];
    $allowed_http_origins   = implode(',', config('http.origin.allow'));
    header('Content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: '.$allowed_http_origins, true);

    $status = [
        200 => '200 OK',
        400 => '400 Bad Request',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        ];

    // ok, validation error, or failure
    header(
        vsprintf('Status: %1s %2s', [
         $code,
         $status[$code]
        ])
    );

    $returnParam['status'] = $code < 300;

    foreach ($message as $key => $value) {
        $returnParam[$key] = $value;
    }

    unset($returnParam['success']);


    // return the encoded json
    print json_encode($returnParam, true);
}


public static function message(){
        return static::$message;
}


}