<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Log Logger
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\HtmlFormatter;
use Tatocaster\Monolog\Formatter\JsonPrettyUnicodePrintFormatter;

class Log{

const DEBUG = 100;

const INFO = 200;

const NOTICE = 250;

const WARNING = 300;

const ERROR = 400;

const CRITICAL = 500;

const ALERT = 550;

const EMERGENCY = 600;

const API = 1;

private $class;

public function __construct($type = null, $level){
	$level = (is_null($level)) ? self::DEBUG : $level;

	$logger = new Logger($type);
	$path = vsprintf('%s%s.%s', [LOGS, $type, 'log']);
	$formatter = new JsonPrettyUnicodePrintFormatter(1, true);

	$stream = new StreamHandler($path , $level);
	$stream->setFormatter($formatter);
	$logger->pushHandler($stream);
	$logger->pushHandler(new FirePHPHandler());

	$this->class = $logger;
}

public function get($type = 'order'){
	  $path = vsprintf('%s%s.%s', [LOGS, $type, 'log']);
	  $file = file_get_contents($path, true);

	  $data = "[\n";
	  $data .= mb_substr($file, 0, -2);
	  $data .= "\n]";
	  $data = json_decode($data, true);

	  	return $data;
}

public function __call($name, $args) {
	call_user_func_array(array($this->class, $name), $args);
}

}