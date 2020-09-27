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
if(!defined('IS_TEST')) define('IS_TEST', false);

function onShutDown() {
	$last_error = error_get_last();
	if(is_array($last_error) && in_array($last_error['type'], [E_PARSE,E_ERROR,E_USER_ERROR,E_CORE_ERROR,E_COMPILE_ERROR])) {
		try {
			AppErrorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
			if(IS_TEST) test_finish();
			return;
		} catch(\Throwable $e) {
			if(IS_TEST) test_finish();
			return;
		}
		
	}
	if(IS_TEST) {
		set_test_status(true);
		test_finish();
	}
	
	
	
}

function onErrorHandler($errno, $errstr, $errfile, $errline) {
	AppErrorHandler($errno, $errstr, $errfile, $errline);
}

function AppErrorHandler($type, $message, $file, $line) {
	$err_message = sprintf("%s\nFile %s Line %d", $message, $file, (int) $line);
	
	if(DISPLAY_ERRORS === 1) echo $err_message . "\n";
	
	$err_fatal = (bool) (($type === E_ERROR || $type === E_USER_ERROR) || THROW_ERR_ANY);
	
	$error_code = null;
	if($err_fatal) {
		if(IS_TEST) set_test_status(false);
		http_response_code(500);
		if(class_exists(\TinyMvc\Service\Response::class)) \TinyMvc\Service\Response::$HTTP_RESPONSE_CODE_SETTED = true;
	}
	if(function_exists('service_exists') && service_exists('log')) {
		$error_code = \TinyMvc\Service\Log::$unique;
		log_e('Bootstrap', $err_message);
	} else if(IS_TEST) add_test_log_count(1);
	if(CLI_CONSOLE && (int) DISPLAY_ERRORS === 0) echo $err_message . "\n";
	
	if($err_fatal) {
		if(!CLI_CONSOLE && DISPLAY_ERRORS === 0 && defined('FATAL_ERROR_HTML_PATH') && is_file(FATAL_ERROR_HTML_PATH) && is_readable(FATAL_ERROR_HTML_PATH)) show500ErrorTemplate(FATAL_ERROR_HTML_PATH, $error_code);
		if(!IS_TEST) die();
	}
}

function show500ErrorTemplate(string $path, string $error_code = null) {
	ob_end_clean();
	if($error_code !== null) {
		include($path);
	} else echo file_get_contents($path);
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

