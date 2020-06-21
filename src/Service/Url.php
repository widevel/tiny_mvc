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

class Url {
	public $base_url, $segments, $arguments;
	public function __construct() {
		$this->base_path = $this->getBasePath();
		$this->base_url = $this->getBaseUrl();
		$this->segments = array_key_exists('path', $_GET) ? array_values(array_filter(explode('/', $_GET['path']))) : [];
		$this->arguments = $this->segments;
		if(count($this->arguments) > 0) {
			if(count($this->arguments) == 1) unset($this->arguments[0]);
			if(count($this->arguments) >= 2) unset($this->arguments[0], $this->arguments[1]);
			
			$this->arguments = array_values($this->arguments);
		}
	}
	
	public function getSegment($n) { return array_key_exists((int) $n, $this->segments) ? $this->segments[(int) $n] : null; }
	public function getSegments() { return $this->segments; }
	public function getSegmentsCount() { return count($this->segments); }
	
	public function getArgument($n) { return array_key_exists((int) $n, $this->arguments) ? $this->arguments[(int) $n] : null; }
	public function getArguments() { return $this->arguments; }
	public function getArgumentsCount() { return count($this->arguments); }
	
	public function getBaseUrl() {
		$port = (int) $_SERVER['SERVER_PORT'];
		$hostname = $_SERVER['HTTP_HOST'];
		$is_https = (bool) (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
		
		$http_part = $is_https ? 's' : '';
		$port_part = ($port == 80 && !$is_https) || ($port == 443 && $is_https) ? '' : ':' . $port;
		$hostname_part = $hostname . $port_part;
		
		return sprintf('http%s://%s%s', $http_part, $hostname_part, $this->base_path);
	}
	
	public function getUrl($uri) {
		return $this->getBaseUrl() . $uri;
	}
	
	private function getBasePath() {
		$base_path =  array_key_exists('PATH_INFO', $_SERVER) ? substr($_SERVER['PHP_SELF'], 0, -strlen($_SERVER['PATH_INFO'])) : $_SERVER['PHP_SELF'];
		
		if(basename($base_path) == 'index.php') $base_path = substr($base_path,0,-strlen('index.php'));
		
		if(substr($base_path,-1) != '/') $base_path .= '/';
		
		return $base_path;
	}

}