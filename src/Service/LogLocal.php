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
use Widevel\SmartlogClient\Log as SmartLog;

class LogLocal {
		
	private $path;

	public static $pending_logs = [];
	
	public function __construct() {
		$this->path = service('path')->logs;

		foreach(self::$pending_logs as $log) {
			call_user_func_array([$this, 'write'], $log);
		}
	}
	
	public function write(int $level, $message = null, string $name = null, array $tags = [], $data = null, \DateTime $date = null) {
		$level_str = null;
		switch($level) {
			case SmartLog::LEVEL_INFO:
				$level_str = 'info';
				break;
			case SmartLog::LEVEL_DEBUG:
				$level_str = 'debug';
				break;
			case SmartLog::LEVEL_WARNING:
				$level_str = 'warning';
				break;
			case SmartLog::LEVEL_ERROR:
				$level_str = 'error';
				break;
		}

		$date = $date === null ? new \DateTime('now') : $date;

		$line_str = sprintf("[%s] (%s) (%s) (%s): (%s) (%s)", $date->format('Y-m-d H:i:s'), $level_str, $name, implode(',', $tags), $message, self::visualData($data)) . "\n";

		self::log_file_write($this->path . $level_str . '_' . date('Y-m-d_H') . '.log', $line_str);
		
		//$smartlog_arguments = [$message, $name, $tags, $data];
		//call_user_func_array('\TinyMvc\Service\SmartLog::' . $level_str, $smartlog_arguments);
	}

	private static function log_file_write($path, $str) :bool {
		if(!$fp = fopen($path, 'ab')) {
			return false;
		}
		
		flock($fp, LOCK_EX);
		fwrite($fp, $str);
		flock($fp, LOCK_UN);
		fclose($fp);
		
		return true;
	}

	private static function visualData($data) {

		if(is_bool($data)) return $data ? 'true' : 'false';
		if(\is_resource($data)) return 'Resource';

		return print_r($data, true);
	}
}