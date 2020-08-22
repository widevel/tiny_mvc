<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

if(!defined('CLI_CONSOLE')) define('CLI_CONSOLE', 0);

function onShutDown() {
	$last_error = error_get_last();
	if(!is_array($last_error)) return;
	if($last_error['type'] === E_ERROR || $last_error['type'] === E_USER_ERROR) {
		AppErrorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
	}
}

function onErrorHandler($errno, $errstr, $errfile, $errline) {
	AppErrorHandler($errno, $errstr, $errfile, $errline);
}

function AppErrorHandler($type, $message, $file, $line) {
	$err_message = sprintf("%s\nFile %s Line %d", $message, $file, (int) $line);
	
	$err_fatal = (bool) (($type === E_ERROR || $type === E_USER_ERROR) || THROW_ERR_ANY);
	
	if($err_fatal) {
		http_response_code(500);
		define('HTTP_RESPONSE_CODE_SETTED', 1);
	}
	if(function_exists('service_exists') && service_exists('log')) {
		log_e($err_message);
	}
	
	if($err_fatal) die();
}

register_shutdown_function('onShutDown');

set_error_handler('onErrorHandler');

$composer_autoload_file = realpath('../composer/vendor/autoload.php');

if($composer_autoload_file !== false) require_once $composer_autoload_file;
require_once 'autoload.php';

$bundlename_file = realpath(__DIR__ . '/../bundle.name');
if($bundlename_file === false) throw new Exception('File bundle.name not exists');
define('BUNDLE_NAME',  file_get_contents($bundlename_file));

load_php_files('src');
load_php_files(__DIR__ . '/../' . BUNDLE_NAME);

$bootstrap_class = new TinyMvcBootstrap;
$bootstrap_class->load_services();

