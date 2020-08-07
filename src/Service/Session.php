<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
namespace TinyMvc\Service;

class Session {
	
	const COOKIE_LIFETIME = 3600 * 24 * 7;
	
	public function __construct() {
		if(session_status() != PHP_SESSION_ACTIVE) session_start(['cookie_lifetime' => SELF::COOKIE_LIFETIME]);
	}
}