<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function log_e() { 
	$arguments = func_get_args();
	array_unshift($arguments , Widevel\SmartlogClient\Log::LEVEL_ERROR);
	return call_user_func_array('log_write', $arguments);
}

function log_w() { 
	$arguments = func_get_args();
	array_unshift($arguments , Widevel\SmartlogClient\Log::LEVEL_WARNING);
	return call_user_func_array('log_write', $arguments);
}

function log_d() { 
	$arguments = func_get_args();
	array_unshift($arguments , Widevel\SmartlogClient\Log::LEVEL_DEBUG);
	return call_user_func_array('log_write', $arguments);
}

function log_i() { 
	$arguments = func_get_args();
	array_unshift($arguments , Widevel\SmartlogClient\Log::LEVEL_INFO);
	return call_user_func_array('log_write', $arguments);
}

function log_write(int $level, $message = null, string $name = null, array $tags = [], $data = null, \DateTime $date = null) {
	$arguments = [$level, $message, $name, $tags, $data, $date];
	if(service_exists('log')) {
		return call_user_func_array([service('log'), 'write'], $arguments);
	} else {
		$arguments[(count($arguments) - 1)] = new \DateTime('now');
		\TinyMvc\Service\LogLocal::$pending_logs[] = $arguments;
	}
}