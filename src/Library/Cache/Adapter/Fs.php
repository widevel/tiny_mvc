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
		$path = $this->getFilePath($name);
		if($this->exists($name) && is_file($path)) return unlink($path);
		return false;
	}
	
	private function getFilePath(string $name) :string {
		$path = $this->base_path . cache_parse_name($name);
		$dir_path = dirname($path);
		if(!is_dir($dir_path)) mkdir($dir_path, 0777, true);
		return $path;
	}
}