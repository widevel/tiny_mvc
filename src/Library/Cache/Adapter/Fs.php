<?php

namespace TinyMvc\Library\Cache\Adapter;

class Fs {
	
	private $base_path;
	
	public function __construct() {
		$this->base_path = service('path')->cache;
	}
	
	public function exists(string $name) :bool {
		return is_file($this->getFilePath($name));
	}
	
	public function get(string $name) {
		return $this->exists($name) ? file_get_contents($this->getFilePath($name)) : null;
	}
	
	public function set(string $name, string $value) {
		return file_put_contents($this->getFilePath($name), $value);
	}
	
	public function del(string $name) :bool {
		if($this->exists($name)) return unlink($this->getFilePath($name));
		return false;
	}
	
	private function getFilePath(string $name) :string {
		return $this->base_path . cache_parse_name($name);
	}
}