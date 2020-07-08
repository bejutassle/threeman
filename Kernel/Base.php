<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Base Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

use Rewrite as Rewrite;

class Base{

public function __construct(){
	$baseName = vsprintf('\Base\\%1s', [ucfirst(Rewrite::requestGroup())]);
	$baseClass = new $baseName();
}

public function __destruct(){

}

}