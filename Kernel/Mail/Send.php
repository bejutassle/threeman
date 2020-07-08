<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Send Mail
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Mail;

use \Core\Database as Database;
use \Core\Template as Template;
use \Core\Mail as Mail;
use \Helper\Router as Router;
use \Middleware\Base as Middleware;
use \Carbon\Carbon as Time;

class Send{

protected $db;
protected $user;
protected $mail = array();

public function __construct(){
	Router::setRegister(true);
	Template::addPath(tplDirectory('mail'), config('cache.template.mail'));
	$this->db = Database::get();
	$this->user = Middleware::get();
}

public function sendContact($vars = array()){
		$data = $vars[0];

		Template::append('data', $data);

		array_push($this->mail, [
		'setFrom' => [
			'title' => option('title'), 
			'address' => config('mail.noreplyAddress')
		],

		'addAddress' => [
			config('mail.supportAddress'),
			$data['email']
		],

		'subject' => $data['title'],
		'send' => Template::render('main/', 'contact'),
		]);
}

public function sendOrder($vars = array()){
		$data = $vars[0];

		Template::append('data', $data);

		array_push($this->mail, [
		'setFrom' => [
			'title' => option('title'), 
			'address' => config('mail.noreplyAddress')
		],

		'addAddress' => [
			config('mail.supportAddress'),
			$data['email']
		],

		'subject' => $data['title'],
		'send' => Template::render('main/', 'order'),
		]);
}

public function __destruct(){
		$this->mail = $this->mail[0];

		Mail::setFrom($this->mail['setFrom']['title'], $this->mail['setFrom']['address']);
		Mail::addAddress($this->mail['addAddress'][0]);
		if(!empty($this->mail['addAddress'][1])) Mail::addAddress($this->mail['addAddress'][1]);
		if(!empty($this->mail['addAddress'][2])) Mail::addAddress($this->mail['addAddress'][2]);
		if(!empty($this->mail['addAddress'][3])) Mail::addAddress($this->mail['addAddress'][3]);
		if(!empty($this->mail['addAddress'][4])) Mail::addAddress($this->mail['addAddress'][4]);
		Mail::subject($this->mail['subject']);
		Mail::send($this->mail['send']);
}

}