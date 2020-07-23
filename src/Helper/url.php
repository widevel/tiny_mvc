<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
function is_url(string $url) {
	return substr($url,0,7) == 'http://' || substr($url,0,8) == 'https://';
}

function add_params_url(string $url, array $params) {
	$prefix = (parse_url($url, PHP_URL_QUERY) === null) ? '?' : '&';
	$query = [];
	foreach($params as $param_name => $param_value) $query[] = $param_name . '=' . $param_value;
	return $url . (count($query) > 0 ? $prefix : '') . implode('&', $query);
}