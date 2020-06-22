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
	if($last_error['type'] === E_ERROR || $last_error['type'] === E_USER_ERROR) {
		http_response_code(500);
		if(function_exists('service_exists') && service_exists('log')) {
			service('log')->error(sprintf("%s\nLine %d File %s", $last_error['message'], (int) $last_error['line'], $last_error['file']), 'error');
		}
	}
}

register_shutdown_function('onShutDown');
 
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

