<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
$GLOBALS['cookies_list'] = IS_TEST ? test_instance()->getCookies() : $_COOKIE;

function cookie_exists(string $name) : bool { return array_key_exists($name, $GLOBALS['cookies_list']); }

function cookie_get(string $name) { return array_key_exists($name, $GLOBALS['cookies_list']) ? $GLOBALS['cookies_list'][$name] : null; }

function cookie_set(string $name, string $value, int $expires) {
	if(IS_TEST) {
		test_instance()->setCookie($name, $value);
	} else {
		$GLOBALS['cookies_list'][$name] = $value;
		if(!headers_sent()) {
			setcookie($name, $value, [
				'expires' => time() + $expires,
				'path' => service('url')->base_path,
				'samesite' => 'Lax'
			]);
		}
	}
	
}