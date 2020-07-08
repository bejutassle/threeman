<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Base Middleware
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Middleware;

use Support\Str as Str;
use Rewrite as Rewrite;

class Base{

public static function get(){
	return Rewrite::requestMiddlewareGroup();
}

}