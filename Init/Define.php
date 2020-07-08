<?php
if (!defined('_JEXEC')): 
header('HTTP/1.0 403 Forbidden');
exit();
endif;

define('ROOT', dirname(dirname(__FILE__)) . '/');

define('INIT', ROOT.'Init/');

define('LIBS', ROOT.'Libs/');

define('BIN', ROOT.'Bin/');

define('APP', ROOT.'App/');

define('ROUTER', APP.'Router/');

define('KERNEL', ROOT.'Kernel/');

define('HELPER', KERNEL.'Helper/');

define('HTTP', KERNEL.'Http/');

define('LANGUAGE', HTTP.'Language/');

define('THEMES', ROOT.'Themes/');

define('STORAGE', ROOT.'Storage/');

define('CONFIG', APP.'Config/');

define('CONTROLLER', APP.'Controller/');

define('VENDOR', LIBS.'vendor/');

define('PATCH', LIBS.'patch/');

define('CACHE', STORAGE.'cache/');

define('LOGS', STORAGE.'logs/');

define('SESSION', STORAGE.'session/');

define('MEDIA', STORAGE.'media/');

define('msg_extension', '<b>"{{extension}}"</b> PHP eklentisi kurulu değil!');

define('msg_settings', '<b>"{{setting}}"</b> PHP seçeneği aktif değil!');

define('msg_version', 'PHP sürümü minimum <b>"{{version}}"</b> olmalıdır!');