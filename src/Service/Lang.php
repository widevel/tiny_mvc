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

class Lang {
	
	CONST LOG_TAG = 'TinyMvcServiceLang';
	
	public $current_locale = 'es_ES';
	private $list = [];
	public function __construct() {
		
		$this->loadLocale();
		
	}
	
	private function loadLocale() {
		$this->list = [];
		$locale_path = service('path')->lang . $this->current_locale . DIRECTORY_SEPARATOR;

		if(is_dir($locale_path)) {

			foreach(scandir($locale_path) as $file) {
				$fullpath = $locale_path . $file;
				if(!is_file($fullpath) || (pathinfo($file, PATHINFO_EXTENSION) != 'yml' && pathinfo($file, PATHINFO_EXTENSION) != 'yaml')) continue;
				$name = basename($file, '.' . pathinfo($file)['extension']);
				
				$data = yaml_parse_file($fullpath);
				
				if(!is_array($data)) throw new \Exception(sprintf('Unable to parse yaml file %s', $fullpath));
				
				$this->list[$name] = $data;
			}
		}
	}
	
	public function setLocale(string $locale) {
		$this->current_locale = $locale;
		$this->loadLocale();
	}
	
	public function get(string $tag, string $name) {
		
		if(array_key_exists($tag, $this->list) && array_key_exists($name, $this->list[$tag])) {
			return $this->list[$tag][$name];
		}
		
		log_w(self::LOG_TAG, 'Unable to find %s.%s lang var', $tag, $name);

		return null;
	}
	
}