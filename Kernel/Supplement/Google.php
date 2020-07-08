<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Google Supplement
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Supplement;

class Google{

public function validateRecaptcha($post){

    $buildQuery = $this->buildRecaptcha($post);
    $optsQuery = $this->optionsRecaptcha($buildQuery);
    $context  = stream_context_create($optsQuery);
    $response = file_get_contents(config('google.recaptcha.url'), false, $context);
    $result = json_decode($response);

    return $result;

}

private function buildRecaptcha($post){


$buildQuery = http_build_query(
    array(
        'secret' => config('google.recaptcha.secret'),
        'response' => $post,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    )
);

        return $buildQuery;

}

private function optionsRecaptcha($buildQuery){

$optsQuery = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $buildQuery
    )
);

        return $optsQuery;

}


}