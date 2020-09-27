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
	const LINE_FORMAT = '[%s.%s] (%s) (%s) (%s) (ctx_%s): '."\n".'%s';
	
	private $path;
	
	public static $unique;
	
	public function __construct() {
		self::$unique = generate_random_hash();
		$this->path = service('path')->logs;
		if(IS_TEST) {
			$this->path .= 'Test' . DIRECTORY_SEPARATOR;
			if(!is_dir($this->path)) mkdir($this->path, 0777, true);
		}
	}
	
	public function debug() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_DEBUG, microtime(true), null, null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	public function warning() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_WARNING, microtime(true), null, null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	public function error() :bool {
		if(func_num_args() < 2) throw new \Exception('2 arguments minimum are required');
		$args = array_merge([self::LEVEL_ERROR, microtime(true), null, null], func_get_args());
		return call_user_func_array([$this, 'write'], $args);
	}
	
	private function write() :bool {
		
		$arguments = func_get_args();
		
		$level = $arguments[0];
		
		if($level > DEBUG_MAX_LEVEL) return false;
		
		$microtime = $arguments[1];
		$context = $arguments[2];
		$tag_context = $arguments[3];
		$tag = $arguments[4];
		$message = $arguments[5];
		
		$sprintf_args = func_num_args() > 6 ? array_slice($arguments, 6, count($arguments)) : [];
		
		if(count($sprintf_args) > 0) {
			$message = call_user_func_array('sprintf', array_merge([$message], log_format_sprintf_args($sprintf_args)));
		}
		
		$original_context = $context;
		$context = $tag !== null && $context === null ? generate_random_hash() : $context;
		
		$tag = $tag === null ? log_get_level_name($level) : log_format_tag($tag);
		
		$time = get_time_from_microtime_func($microtime);
		
		$final_tag_name = $tag_context !== null ? $tag_context : $tag;
		
		$line_str = log_generate_line(
			self::LINE_FORMAT,
			self::LINE_DATE_FORMAT,
			self::$unique,
			$level,
			$final_tag_name,
			$context,
			$message,
			$time,
			$microtime,
		);
		
		if(IS_TEST) add_test_log_count($level);
		
		$file_rel_path = stripos($tag, '.') !== false ? str_replace('.', DIRECTORY_SEPARATOR, $tag) : $tag;
		
		$file_dir = $this->path . dirname($file_rel_path);

		if(!is_dir($file_dir)) mkdir($file_dir, 0777, true);
		
		$date_formated = date(self::FILENAME_DATE_FORMAT, $time);
		
		$filepath = $this->path . sprintf(self::FILENAME_WTAG_FORMAT, $file_rel_path, $date_formated);
		
		$response = log_file_write($filepath, "\n" . str_replace("\n", "\n\t", $line_str));
		
		if($context !== null && $original_context === null) {

			$args = func_get_args();
			$args[2] = $context;
			$args[3] = $tag;
			$args[4] = null;
			$args[5] = sprintf('(Tag %s)'."\n".'%s', $tag, $message);
			$args = array_slice($args, 0, 6);
			return call_user_func_array([$this, 'write'], $args);
		}
		
		return $response;
		
	}
}