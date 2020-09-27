<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function php_session_get_all() :array {
	if(IS_TEST) {
		return test_instance()->getSessions();
	} else {
		return isset($_SESSION) && is_array($_SESSION) ? $_SESSION : [];
	}
}

function php_session_get(string $name) {
	if(IS_TEST) {
		return test_instance()->getSession($name);
	} else {
		return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : null;
	}
}

function php_session_set(string $name, $value) {
	if(IS_TEST) {
		if(is_object(test_instance())) test_instance()->setSession($name, $value);
	} else {
		$_SESSION[$name] = $value;
	}
}

function php_session_del(string $name) {
	if(IS_TEST) {
		if(is_object(test_instance())) test_instance()->delSession($name);
	} else {
		if(array_key_exists($name, $_SESSION)) unset($_SESSION[$name]);
	}
}