<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
$GLOBALS['cookies_list'] = $_COOKIE;

function cookie_exists(string $name) : bool { return array_key_exists($name, $GLOBALS['cookies_list']); }

function cookie_get(string $name) { return array_key_exists($name, $GLOBALS['cookies_list']) ? $GLOBALS['cookies_list'][$name] : null; }

function cookie_set(string $name, string $value, int $expires) {
	$GLOBALS['cookies_list'][$name] = $value;
	setcookie($name, $value, time() + $expires, service('url')->base_path);
}