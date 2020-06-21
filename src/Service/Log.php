<?php
/**
 * PHP version 7.X
 *
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
	const LINE_FORMAT = '[%s.%s] (%s) (%s): %s';
	
	private $unique, $path;
	
	public function __construct() {
		$this->unique = generate_random_hash();
		$this->path = service('path')->logs;
	}
	
	public function debug($message, string $tag = null) :bool {
		return $this->write(3, $message, microtime(true), $tag);
	}
	
	public function warning($message, string $tag = null) :bool {
		return $this->write(2, $message, microtime(true), $tag);
	}
	
	public function error($message, string $tag = null) :bool {
		return $this->write(1, $message, microtime(true), $tag);
	}
	
	private function write(int $level, $message, float $microtime, string $tag = null) :bool {
		if($level > DEBUG_MAX_LEVEL) return false;
		if($tag !== null) $tag = log_format_tag($tag);
		$time = get_time_from_microtime_func($microtime);
		$line_str = log_generate_line(
			self::LINE_FORMAT,
			self::LINE_DATE_FORMAT,
			$this->unique,
			$level,
			$message,
			$time,
			$microtime,
		);
		
		$date_formated = date(self::FILENAME_DATE_FORMAT, $time);
		
		$filepath = $tag === null ? sprintf(self::FILENAME_FORMAT, $date_formated) : sprintf(self::FILENAME_WTAG_FORMAT, $tag, $date_formated);
		$filepath = $this->path . $filepath;
		
		return log_file_write($filepath, $line_str);
		
	}
}