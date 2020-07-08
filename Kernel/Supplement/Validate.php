<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Validation Supplement
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Supplement;

use Validation\Validator;
use Validation\Rules;
use Core\Database as Database;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Request as Request;

class Validate{

protected $validation;
protected $request;
protected $messages;
protected $aliases;
protected $replace = ':attribute'; 
protected $rules = array();
protected $errors = array();
protected $allerrors = array();

public function __construct($request){
		$this->request = $request;
		$this->messages = vsprintf('%1s%2s/%3s.php', [LANGUAGE, config('language.default'), 'validation']);
		$this->aliases = vsprintf('%1s%2s/%3s.php', [LANGUAGE, config('language.default'), 'aliases']);
}

public function setRules($name, $rule){
		$this->rules[$name] = $rule;
}

public function addErrors($input, $name){
		$msg = require($this->messages);
		$alias = require($this->aliases);
		$text = (!empty($msg[$name])) ? $msg[$name] : $name;
		$label = (!empty($alias[$input])) ? $alias[$input] : $input;
		$name = str_replace($this->replace, $label, $text);
		$this->errors[$input] = $name;
}

public function errors(){
		$this->validation = new Validator;
		$validate = $this->validation->make($this->request, $this->rules);
		$validate->setMessages(require($this->messages));
		$validate->setAliases(require($this->aliases));
		$validate->setTranslations(require($this->aliases));
		$validate->validate();

		return $validate->errors()->firstOfAll()+$this->errors;
}

}