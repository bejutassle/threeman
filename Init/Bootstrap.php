<?php 
/**
 * THREEMAN  / Web Application BOOTSTRAP.
 * @author Emre Emir <emre.emir@hoops.com.tr>
 * @package WebApp Bootstrap
 * @copyright  It is protected by the GNU General Public License
 * All Rights Reserved
 */

if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

use \Composer\Autoload\ClassLoader;

abstract class Boot{

public static function fsprintf($str, $data) {
    return preg_replace_callback('#{{(\w+?)(\.(\w+?))?}}#', function($m) use ($data){
        return count($m) === 2 ? $data[$m[1]] : $data[$m[1]][$m[3]];
    }, $str);
}

public static function Controller(){
	$phpv = (string) '7.2.18';
	$msg_extension = msg_extension;
	$msg_settings = msg_settings;
	$msg_version = msg_version;
	$in_extensions = ['bcmath', 'calendar', 'ctype', 'date', 'filter', 'hash', 'iconv', 'json', 'SPL', 'pcre', 'readline', 'session', 'standard', 'mysqlnd', 'tokenizer', 'zip', 'zlib', 'libxml', 'dom', 'PDO', 'bz2', 'SimpleXML', 'xml', 'wddx', 'xmlreader', 'xmlwriter', 'openssl', 'curl', 'fileinfo', 'gd', 'gettext', 'gmp', 'intl', 'imap', 'ldap', 'mbstring', 'exif', 'mysqli', 'pdo_mysql', 'soap', 'sockets', 'sqlite3', 'xmlrpc', 'xsl', 'Zend OPcache'];
	$in_settings = ['allow_url_fopen', 'file_uploads'];

	array_filter($in_extensions, function ($val, $key) use ($msg_extension) {
		if(!extension_loaded($val))
			try {
			    throw new Exception(self::fsprintf($msg_extension, [
			    	'extension' => strtoupper($val)
			    ]));
			} catch(Error | Exception $e) {
				die(self::fsprintf('<style>html,body,.container{height:100%}.container{position:relative;text-align:center}.container>p{position:absolute;top:50%;left:0;right:0;margin-top:-9px}</style><div class="container"><p>{{msg}}<br><b>Code:</b> {{code}}</p></div>', [
					'msg' => $e->getMessage(),
					'code' => $e->getCode(),
				]));
			}
	}, ARRAY_FILTER_USE_BOTH);

	array_filter($in_settings, function ($val, $key) use ($msg_settings) {
		if(!ini_get($val))
			try {
			    throw new Exception(self::fsprintf($msg_settings, [
			    	'setting' => strtoupper($val)
			    ]));
			} catch(Error | Exception $e) {
				die(self::fsprintf('<style>html,body,.container{height:100%}.container{position:relative;text-align:center}.container>p{position:absolute;top:50%;left:0;right:0;margin-top:-9px}</style><div class="container"><p>{{msg}}<br><b>Code:</b> {{code}}</p></div>', [
					'msg' => $e->getMessage(),
					'code' => $e->getCode(),
				]));
			}
	}, ARRAY_FILTER_USE_BOTH);

	if(version_compare(PHP_VERSION, $phpv) < 0){
		try {
		    throw new Exception(self::fsprintf($msg_version, [
		    	'version' => $phpv
		    ]));
		} catch(Error | Exception $e) {
				die(self::fsprintf('<style>html,body,.container{height:100%}.container{position:relative;text-align:center}.container>p{position:absolute;top:50%;left:0;right:0;margin-top:-9px}</style><div class="container"><p>{{msg}}<br><b>Code:</b> {{code}}</p></div>', [
					'msg' => $e->getMessage(),
					'code' => $e->getCode(),
				]));
		}
	}

}

public static function Autoload($class) {
	$vendor = [KERNEL, PATCH, CONTROLLER];
	$loader = new ClassLoader();
	$loader->add($class, $vendor);
	$loader->register();
	$loader->setUseIncludePath(true);
	$loader->setClassMapAuthoritative(false);

	return $loader;
}

public static function Load($dir = NULL, $file = NULL, $ext = '.php'){
	$dir = (!empty($dir)) ?  $dir : '';
	$file = (!empty($file)) ? $file : '';
	$path = vsprintf('%s%s%s', [$dir, $file, $ext]);

	if( (!empty($dir)) && (!empty($file)) && (($file === '~')) ):

	foreach (glob($dir.'*'.$ext) as $docs):
	    	require_once($docs);
	endforeach;
	
	else:
			require_once($path);
	endif;
}

public static function AutoLoadRegister($class){
	spl_autoload_register($class);
}

}