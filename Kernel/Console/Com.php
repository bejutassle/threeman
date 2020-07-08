<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package CLI Controller
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Console;

use Support\File as File;
use Core\Database as Database;
use Mail\Send as SendMail;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Colors;
use splitbrain\phpcli\Options;
use splitbrain\phpcli\TableFormatter;

class Com extends CLI{

protected $db;

// register options and arguments
protected function setup(Options $options){
    $options->setHelp('`php threeman <OPTIONS>´ you can consider the example command. CLI Deploy by, Emre Emir.');
    $options->registerOption('version', 'Print Software Version', 'v');
    $options->registerOption('software', 'Software Information', 's');
    $options->registerOption('ini', 'PHP Version Information', 'i');
    $options->registerOption('sendmail', 'Send <defined> Mail', 'p', 'defined');
    $options->registerOption('clear cache', 'Clear <group/all|asset|sql|theme|image/> Cache Files', 'c', 'group');
    $options->registerOption('recreate file', 'Recreate <file/web app config|apache|nginx/> System Files', 'r', 'file');
    $options->registerOption('maintenance', 'Maintenance Status <status/enabled|disabled/>', 'm', 'status');
    $options->registerOption('environment', 'Environment Variable <check/test|prod|local/>', 'e', 'filter');
}

// implement your code
protected function main(Options $options){
    $this->colors->enable();
    $this->db = Database::get();
    $tf = new TableFormatter($this->colors);
    $tf->setBorder(' | ');

    if($options->getOpt('version')):
    	$this->version();

    elseif($options->getOpt('software')):
		$this->software($tf);

    elseif($options->getOpt('ini')):
		$this->ini($tf);

    elseif($options->getOpt('sendmail')):
        $this->sendMail($options);

    elseif($options->getOpt('clear cache')):
    	$this->clearCache($options);

    elseif($options->getOpt('recreate file')):
    	$this->recreate($options);

    elseif($options->getOpt('maintenance')):
        $this->maintenance($options);

    elseif($options->getOpt('environment')):
        $this->environment($options);

    else:
    	print($options->help());
    endif;

}

private function version(){
    	$version = config('app.software.version');
        $this->info($version);
}

private function software($tf){
    	$software = config('app.software');
    	$software['package'] = 'Web App CLI';
        foreach ($software as $val => $opts) {
            echo $tf->format(
                array('50%', '50%'),
                array($val, $opts),
                array(Colors::C_CYAN, Colors::C_GREEN)
            );
        }
}

private function ini($tf){
        // show a header
        echo $tf->format(
            array('*', '30%', '30%'),
            array('ini setting', 'global', 'local')
        );

		$ini = ini_get_all();
        foreach ($ini as $val => $opts) {
            echo $tf->format(
                array('*', '30%', '30%'),
                array($val, $opts['global_value'], $opts['local_value']),
                array(Colors::C_CYAN, Colors::C_RED, Colors::C_GREEN)
            );
        }
}

private function sendMail($options){
        $vars = $options->getOpt('sendmail');
        $vars = explode(',', $vars);
        $url = json_decode(base64_decode($vars[0]));
        $group = $vars[1];
        $var = json_decode(base64_decode($vars[2]));
        $var = (!empty($var)) ? implode(',', $var) : NULL;

        $key = PRIVATE_KEY; 
        $url = vsprintf('%s&group=%s&vars=%s', [$url, $group, $var]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 400); 
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        $response = curl_exec($ch);
        curl_close($ch);
}

private function clearCache($options){

    	$groups = ['all', 'asset', 'sql', 'theme', 'image'];
    	$group = $options->getOpt('clear cache');

    	if(in_array($group, $groups)):

        switch ($group) {

            case 'all':
                $path = vsprintf('%1s%2s/', [CACHE, 'view']);

                if(File::file()->exists($path)):
                    File::file()->remove($path);
                    $this->success("The {$group} cache directory has been deleted.");
                        $files = array();
                        foreach (File::finder()->directories()->notPath('html')->notPath('css')->notPath('js')->path('out')->in(THEMES) as $file):
                            $filePath = $file->getRealPath();
                            $pathName = $file->getRelativePathname();
                            array_push($files, $filePath);
                            $this->success("The `{$pathName}´ cache directory has been deleted.");
                        endforeach;
                        File::file()->remove($files);
                else:
                            $this->warning("The {$group} cache directory was not found.");
                endif;

            break;

            case 'asset':
                $path = vsprintf('%1s%2s/%3s/', [CACHE, 'view', 'Assets']);

                if(File::file()->exists($path)):
                    File::file()->remove($path);
                    $this->success("The {$group} cache directory has been deleted.");
                        $files = array();
                        foreach (File::finder()->directories()->notPath('html')->notPath('css')->notPath('js')->path('out')->in(THEMES) as $file):
                            $filePath = $file->getRealPath();
                            $pathName = $file->getRelativePathname();
                            array_push($files, $filePath);
                            $this->success("The `{$pathName}´ cache directory has been deleted.");
                        endforeach;
                        File::file()->remove($files);
                else:
                            $this->warning("The {$group} cache directory was not found.");
                endif;

            break;

            case 'sql':
                $path = vsprintf('%1s%2s/%3s/', [CACHE, 'view', 'Data']);

                if(File::file()->exists($path)):
                    File::file()->remove($path);
                    $this->success("The {$group} cache directory has been deleted.");
                else:
                    $this->warning("The {$group} cache directory was not found.");
                endif;

            break;

            case 'theme':
                $path = vsprintf('%1s%2s/%3s/', [CACHE, 'view', 'Facedes']);

                if(File::file()->exists($path)):
                    File::file()->remove($path);
                    $this->success("The {$group} cache directory has been deleted.");
                else:
                    $this->warning("The {$group} cache directory was not found.");
                endif;

            break;

            case 'image':
                $path = vsprintf('%1s%2s/%3s/', [CACHE, 'view', 'Image']);

                if(File::file()->exists($path)):
                    File::file()->remove($path);
                    $this->success("The {$group} cache directory has been deleted.");
                else:
                    $this->warning("The {$group} cache directory was not found.");
                endif;

            break;

        }

    	else:
    		        $this->warning('Invalid group name.');
    	endif;
}

private function recreate($options){
    	$files = ['apache'];
    	$file = $options->getOpt('recreate file');

		/**
		 * 	
		$finder = File::finder();
		$finder->files()->path('html')->name('*.twig')->contains('{{assets_url}}')->in(THEMES);

		//dd($finder);
		foreach ($finder as $file) {
		    // dumps the absolute path
		    $contents = $file->getContents();

		}
		 */

    	if(in_array($file, $files)):

    	switch ($file) {
    		case 'apache':
    				$path = vsprintf('%1s%2s', [ROOT, '.htaccess']);
					File::file()->dumpFile($path, $this->htaccessTPL());
					$this->success("The {$file} file was created.");
    		break;
    	}

    	else:
    		$this->warning('Invalid file name.');
    	endif;

}

private function maintenance($options){
        $status = ['enabled', 'disabled', 'status'];
        $statu = $options->getOpt('maintenance');

        if(in_array($statu, $status)):

        switch ($statu) {
            case ('enabled'):
                    $updateAction['settingMaintenance'] = 1;
                    $this->db->update('settings', $updateAction);
                    $this->success('Maintenance mode enabled.');
            break;

            case ('disabled'):
                    $updateAction['settingMaintenance'] = 0;
                    $this->db->update('settings', $updateAction);
                    $this->error('Maintenance mode disabled.');
            break;

            case ('status'):
                    $maintenance = $this->db->getValue('settings', 'settingMaintenance');
                    $maintenance = ($maintenance == 1) ? 'enabled' : 'disabled';
                    $this->error("Maintenance mode status is {$maintenance}.");
            break;
        }

        else:
                    $this->warning('Invalid Maintenance name.');
        endif;

}

private function environment($options){
        $envs = ['test', 'prod', 'local', 'check'];
        $env = $options->getOpt('environment');

        if(in_array($env, $envs)):

        switch ($env) {
            case ('test'):
                    $this->success("The environment variable is set to `{$env}´");
            break;

            case ('prod'):
                    $this->success("The environment variable is set to `{$env}´");
            break;

            case ('local'):
                    $this->success("The environment variable is set to `{$env}´");
            break;

            case ('check'):
                    $variable = config('app.developer.environment');
                    $this->info("Environment variable: {$variable}");
            break;
        }

        else:
                    $this->warning('Invalid Environment variable.');
        endif;

}

private function htaccessTPL(){

$tpl = '<IfModule mod_rewrite.c>

<IfModule mod_negotiation.c>
    DirectorySlash Off
    Options -MultiViews
    Options +FollowSymLinks
    Options -Indexes
    DirectoryIndex index.php
</IfModule>

    ## Begin - Security
    # Block all direct access for these folders
    RedirectMatch 404 (.git|tests|.AbiSuite|nbproject|.idea)
    # Block access to specific file types for these system folders
    RedirectMatch 404 (vendor)/(.*)\.(txt|xml|md|html|yaml|php|pl|py|cgi|twig|tpl|sh|bat|dist|json|exe)
    # Block access to specific file types for these themes folders
    RedirectMatch 404 (twig|tpl)
    # Block all direct access to .md files:
    RedirectMatch 404 \.md
    # Block access to specific files in the root folder
    RedirectMatch 404 (composer.lock|composer.json|\.htaccess|LICENSE)
    ## End - Security

    RewriteEngine On
    RewriteBase /
    RewriteRule ^images/(.*)/(.*)$ Storage/media/img/$1/$2 [L]
    RewriteRule ^static/(.*)/(.*)$ Storage/$1/$2 [L]
    # Set REQUEST_SCHEME (standard environment variable in Apache 2.4)
    RewriteCond %{HTTPS} off
    RewriteRule .* - [E=REQUEST_SCHEME:http]
    RewriteCond %{HTTPS} on
    RewriteRule .* - [E=REQUEST_SCHEME:https]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.*)$ index.php?$1 [L,QSA]
    ErrorDocument 403 /index.php
    ErrorDocument 404 /index.php
    ErrorDocument 500 /index.php
</IfModule>';

return $tpl;

}

}