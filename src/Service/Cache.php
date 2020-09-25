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

use TinyMvc\Library\Cache\Adapter\Fs as AdapterFs;

class Cache {
	
	const ADAPTER_FILE = 'fs';
	
	const LIFETIME = 3600 * 24 * 30;
	
	private $adapter = self::ADAPTER_FILE, $adapterInstance;
	
	public function __construct() {
		$config = get_config('cache', false);
		
		switch($this->adapter) {
			case self::ADAPTER_FILE:
				$this->adapterInstance = new AdapterFs;
				break;
		}
	}
	
	public function exists(string $name) {
		return $this->adapterInstance->exists($name);
	}
	
	public function get(string $name) {
		$value = $this->adapterInstance->get($name);
		if(is_string($value)) return unserialize(gzinflate($value));
		return null;
	}
	
	public function set(string $name, $value) {
		$value_parsed = gzdeflate(serialize($value), 9);
		return $this->adapterInstance->set($name, $value_parsed);
	}
	
	public function del(string $name) {
		return $this->adapterInstance->get($name);
	}
	
}