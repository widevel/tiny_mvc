<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function cache_parse_name(string $name) {
	return hash('sha256', $name);
}

function cache_rt_create_key($data) {
	$str_data = $data;
	
	if(!is_string($str_data)) $str_data = serialize($str_data);
	
	return md5($str_data);
}

function cache_rt_get(string $name) {
	return service('cache_runtime')->get($name);
}

function cache_rt_set(string $name, $value) {
	return service('cache_runtime')->set($name, $value);
}