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


class CacheRunTime {
	
	private $list = [];
	
	public function get(string $name) {
		return array_key_exists($name, $this->list) ? $this->list[$name] : null;
	}
	
	public function set(string $name, $value) {
		return $this->list[$name] = $value;
	}
	
}