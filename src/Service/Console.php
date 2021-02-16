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
		
		$arguments = $argv;
		
		unset($arguments[0], $arguments[1]);
		$arguments = array_values($arguments);

		log_d("Input arguments", "TinyMvc.Service_Console", [], $arguments);
				
		self::callConsoleClass(CLASS_CONSOLE, $arguments);
	}
	
	public static function callConsoleClass(string $class_name, array $arguments = []) {
		$className = sprintf('\%s\Console\\%s', BUNDLE_NAME, $class_name);
		
		if(!class_exists($className)) {
			throw new \Exception(sprintf('Class %s not exists', $className));
			return;
		}
		
		if(count($arguments) >= 2 && $arguments[0] === 'serialized') $arguments = cmd_unserialize_arguments($arguments[1]);
		
		
		$reflector = new \ReflectionClass($className);
		$reflector->newInstanceArgs($arguments);
	}
}