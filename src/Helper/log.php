<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */

function log_generate_line(string $line_format, string $date_format, string $unique, int $level, $message, int $time, float $microtime) :string {
	return sprintf(
		$line_format,
		date($date_format, $time),
		fillStrLeft(get_microtime($microtime), 4, '0'),
		$unique,
		log_get_level_name($level),
		log_format_message($message)
	) . "\n";
}

function log_file_write($path, $str) :bool {
	if(!$fp = fopen($path, 'ab')) {
		return false;
	}
	
	flock($fp, LOCK_EX);
	fwrite($fp, $str);
	flock($fp, LOCK_UN);
	fclose($fp);
	
	return true;
}

function log_format_tag(string $tag) {
	return str_replace(str_split('<>:"/\|?*'), '_', $tag);
}

function log_get_level_name(int $level) :string {
	switch($level) {
		case 1:
			return 'error';
			break;
		case 2:
			return 'warning';
			break;
		case 3:
			return 'debug';
			break;
		
	}
}

function log_format_message($message) :string {
	switch(gettype($message)) {
		case 'boolean':
			return $message ? 'true' : 'false';
			break;
		case 'integer':
		case 'double':
			return (string) $message;
			break;
		case 'string':
			return $message;
			break;
		case 'array':
		case 'object':
		case 'resource':
			return print_r($message, true);
			break;
		case 'NULL':
			return gettype($message);
			break;
		case 'unknown type':
			return gettype($message);
			break;
	}
}