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

class Config {
	
	public $list = [];
	
	public function get($name, $required = true) {
		
		if(array_key_exists($name, $this->list)) return $this->list[$name];

		$path = service('path')->config . $name . '.yaml';
		if(!is_file($path)) {
			if($required) throw new \Exception(sprintf('Unable to load config file %s', $path));
			return null;
		}
		
		$data = yaml_parse_file($path);
		
		if(!is_array($data)) {
			if($required) throw new \Exception(sprintf('Unable to parse yaml file %s', $path));
			return null;
		}
		
		$this->list[$name] = $data;
		
		return $data;
		
	}
	

}