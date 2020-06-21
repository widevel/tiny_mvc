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

class Request {
	public function post(string $name) {
		return array_key_exists($name, $_POST) ? self::formatValue($_POST[$name]) : null;
	}
	
	public function get(string $name) {
		return array_key_exists($name, $_GET) ? self::formatValue($_GET[$name]) : null;
	}
	
	private static function formatValue($value) {
		switch(gettype($value)) {
			case 'string':
				$json_decode = json_decode($value);
				if($json_decode) {
					return self::formatValue($json_decode);
				}
				return trim($value);
				break;
			case 'array':
				return array_map('self::formatValue', $value);
		}
		
		return $value;
	}

}