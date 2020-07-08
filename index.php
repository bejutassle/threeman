<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package WebApp
 * @copyright  It is protected by the GNU General Public License
 * All Rights Reserved
 */
define('_JEXEC', true);
define('__INIT', dirname(realpath(__FILE__)).'/Init');
ini_set('expose_php', 0);
ini_set('session.name', 'SSID');

error_reporting(E_ALL);
ini_set('display_errors', 1);
/*error_reporting(0);
ini_set('display_errors', 0);*/

require_once(__INIT.'/Define.php');
require_once(__INIT.'/Bootstrap.php');

ob_start();

//ini_set('session.save_path', SESSION);
if(!session_id()) {
    session_start();
}

//System Control
Boot::Controller();
//Composer Autoloader
Boot::Load(VENDOR, 'autoload');
//Kernel(Core) Autoloader
Boot::AutoLoadRegister('Boot::Autoload');
//Config File Loader
Boot::Load(CONFIG, '~');

//Run Web Application
App::Run();

ob_end_flush();