<?php 
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Mail Core
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

namespace Core;

use PHPMailer\PHPMailer\PHPMailer as Mailer;
use PHPMailer\PHPMailer\Exception as Exception;

class Mail{

protected static $mail;
protected static $config;

public static function get(){
		static::$config = config('mail');
		if(static::$config['type'] == 'smtp'){
		static::$mail = new Mailer();

		static::$mail->isSMTP(); // Set mailer to use SMTP
		static::$mail->Timeout = static::$config['timeout'];
		static::$mail->Host = static::$config['host'];  // Specify main and backup SMTP servers
		static::$mail->Port = static::$config['port'];
		static::$mail->SMTPDebug = static::$config['debug'];
		static::$mail->SMTPAuth = static::$config['auth']; // Enable SMTP authentication
		static::$mail->SMTPSecure = static::$config['secure']; // Enable TLS encryption, `ssl` also accepted
		static::$mail->SMTPAutoTLS = static::$config['autoTLS'];
		static::$mail->Username = static::$config['username']; // SMTP username
		static::$mail->Password = static::$config['password']; // SMTP password
		static::$mail->CharSet = 'UTF-8';
		}else{
		static::$mail = new Mailer;
		static::$mail->isSendmail();
		}
}

public static function setFrom($name = NULL, $address = NULL){
	$getAddress = (!empty($address)) ? $address : static::$config['senderAddress'];
	static::$mail->setFrom($getAddress, $name);
}

public static function addAddress($address = NULL, $name = NULL){
	static::$mail->addAddress($address, $name);
}

public static function addReplyTo($address = NULL, $name = NULL){
	static::$mail->addReplyTo($address, $name);
}

public static function addCC($address = NULL){
	static::$mail->addCC($address);
}

public static function addBCC($address = NULL){
	static::$mail->addBCC($address);
}

public static function addAttachment($file = NULL){
	static::$mail->addAttachment($file);
}

public static function subject($var = NULL){
	static::$mail->Subject = $var;
}

public static function send($var = NULL){
	try {
	static::$mail->isHTML(true);
	static::$mail->Body = $var;
	static::$mail->send();
	} catch (Exception $e) {
	    print(static::$mail->ErrorInfo);
	}
}

public static function error(){
	return static::$mail->ErrorInfo;
}

public static function save(){
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, static::$mail->Username, static::$mail->Password);
    $result = imap_append($imapStream, $path, static::$mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}

}