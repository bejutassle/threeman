<?php 

use Core\Config;


/**
 * Setup the Mail configuration.
 */
Config::set('mail', [

        'type' => (string) option('mail-type'),
        'host' => (string) option('mail-host'),
        'port' => (int) option('mail-port'),
        'secure' => (string) option('mail-secure'),
        'debug' => 0,
        'auth' => (boolean) option('mail-smtp-auth'),
        'autoTLS' => false,
        'timeout' => option('mail-timeout'),
        'username' => option('mail-user'),
        'password' => decrypt(option('mail-password')),
        'supportAddress' => option('mail-sender'),
        'noreplyAddress' => option('mail-user'),

]);