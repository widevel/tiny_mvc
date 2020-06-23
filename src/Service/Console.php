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

class Console {
	
	
	public function __construct() {
		global $argv;
		$className = sprintf('\%s\Console\\%s', BUNDLE_NAME, CLASS_CONSOLE);
		
		if(!class_exists($className)) {
			throw new \Exception(sprintf('Class %s not exists', $className));
			return;
		}
		
		$arguments = $argv;
		
		unset($arguments[0], $arguments[1]);
		
		$arguments = array_values($arguments);
		
		$reflector = new \ReflectionClass($className);
		$reflector->newInstanceArgs($arguments);
	}
}