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

class Log {
	
	const LEVEL_DEBUG 		= 3;
	const LEVEL_WARNING 	= 2;
	const LEVEL_ERROR 		= 1;
	
	const FILENAME_DATE_FORMAT = 'Y-m-d_H';
	const FILENAME_WTAG_FORMAT = '%s_%s.log';
	const FILENAME_FORMAT = '%s.log';
	const LINE_DATE_FORMAT = 'Y-m-d H:i:s';
	const LINE_FORMAT = '[%s.%s] (%s) (%s) (ctx_%s): %s';
	const REDUNDANT_LEVELS = [self::LEVEL_WARNING, self::LEVEL_ERROR];
	
	private $path;
	
	public static $unique;
	
	public function __construct() {
		self::$unique = generate_random_hash();
		$this->path = service('path')->logs;
	}
	
	public function debug() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_DEBUG, microtime(true), null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	public function warning() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_WARNING, microtime(true), null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	public function error() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_ERROR, microtime(true), null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	private function write() :bool {
		
		$arguments = func_get_args();
		
		$level = $arguments[0];
		
		if($level > DEBUG_MAX_LEVEL) return false;
		
		$microtime = $arguments[1];
		$context = $arguments[2];
		$tag = $arguments[3];
		$message = $arguments[4];
		
		$sprintf_args = func_num_args() > 5 ? array_slice($arguments, 5, count($arguments)) : [];
		
		if($sprintf_args > 0) {
			$message = call_user_func_array('sprintf', array_merge([$message], log_format_sprintf_args($sprintf_args)));
		}
		
		$original_context = $context;
		$context = $tag !== null && in_array($level, self::REDUNDANT_LEVELS) && $context === null ? generate_random_hash() : $context;
		
		$tag = $tag === null ? log_get_level_name($level) : log_format_tag($tag);
		
		$time = get_time_from_microtime_func($microtime);
		
		$line_str = log_generate_line(
			self::LINE_FORMAT,
			self::LINE_DATE_FORMAT,
			self::$unique,
			$level,
			$context,
			$message,
			$time,
			$microtime,
		);
		
		$file_rel_path = sprintf($tag, '.') !== false ? str_replace('.', DIRECTORY_SEPARATOR, $tag) : $tag;
		
		$file_dir = $this->path . dirname($file_rel_path);

		if(!is_dir($file_dir)) mkdir($file_dir, 0777, true);
		
		$date_formated = date(self::FILENAME_DATE_FORMAT, $time);
		
		$filepath = $this->path . sprintf(self::FILENAME_WTAG_FORMAT, $file_rel_path, $date_formated);
		
		$response = log_file_write($filepath, $line_str);
		
		if(in_array($level, self::REDUNDANT_LEVELS) && $context !== null && $original_context === null) {

			$args = func_get_args();
			$args[2] = $context;
			$args[3] = null;
			$args[4] = sprintf('(Tag: %s) %s', $tag, $message);
			return call_user_func_array([$this, 'write'], $args);
		}
		
		return $response;
		
	}
}