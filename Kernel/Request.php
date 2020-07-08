<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Request Kernel
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use Symfony\Component\HttpFoundation\Request as Httpd;
use Rewrite as Rewrite;

class Request{

protected $_REQUEST = array();
protected $_GET;
protected $_POST;
protected $_FILES;
protected $HTTP;

public function __construct(){
    $this->HTTP = Httpd::createFromGlobals();
    $this->_GET = $this->HTTP->query->all();
    $this->_POST = $this->HTTP->request->all();
    //$this->_FILES = $this->HTTP->files->all();
}

public function queryAll(){
      //Get variable
      $this->_REQUEST = (is_array($this->_GET) && count($this->_GET) > 0) ? array_map(array($this, 'escapeGET'), $this->_GET) : $this->_GET;
      //Post variable
      $this->_REQUEST += (is_array($this->_POST) && count($this->_POST) > 0) ? array_map(array($this, 'escapePOST'), $this->_POST) : $this->_POST;
      //File variable
      $this->_REQUEST += $_FILES;
      //Append variable
      $this->_REQUEST['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
      //Append variable
      $this->_REQUEST['isXHR'] = $this->isXHR();

      return $this->_REQUEST;
}

public function rule($param = 'xhr'){
        $request = $this->queryAll();
        if(($param == 'xhr') && (empty($request['isXHR']))){
            exit(Rewrite::get()->requestMap());
        }
}

private function isXHR(){
      return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
}

private function escapePOST($var = NULL){
      if(is_string($var) || is_int($var)):
        $var = htmlspecialchars($var, ENT_QUOTES);
      endif;

     return $var;
}


private function escapeGET($var = NULL){
      $search = array(";", 
                            "'", 
                            "--", 
                            "\*",
                            "/",
                            "select", 
                            "insert", 
                            "delete", 
                            "update", 
                            "drop table", 
                            "union",
                            "null",
                            "SELECT",
                            "INSERT",
                            "DELETE",
                            "UPDATE",
                            "FROM",
                            "from",
                            "DROP TABLE",
                            "UNION",
                            "NULL",
                            "order by",
                            );

        if(is_string($var) || is_int($var)):
          $var = str_replace($search, '', $var);
          $var = htmlentities($var, ENT_QUOTES);
        endif;

      return $var;
}
  
}