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
	public $base_url, $segments = [], $arguments = [], $base_path, $current_url, $referer;
	public function __construct() {
		$this->base_url = $_ENV['BASE_URL'];
		
		if(CLI_CONSOLE) {
			if(!array_key_exists('URL_BASE_PATH', $_ENV)) throw new \Exception('URL_BASE_PATH not defined in .env');
			if(!array_key_exists('BASE_URL', $_ENV)) throw new \Exception('BASE_URL not defined in .env');
			$this->base_path = $_ENV['URL_BASE_PATH'];
			
		} else {
			$this->base_path = $this->getBasePath();
			//$this->base_url = $this->getBaseUrl();
			
			if(array_key_exists('HTTP_REFERER', $_SERVER)) $this->referer = $_SERVER['HTTP_REFERER'];

			if(array_key_exists('PHP_LOCAL_SERVER', $_ENV) && intval($_ENV['PHP_LOCAL_SERVER']) == 1) {
				$this->segments = array_key_exists('PATH_INFO', $_SERVER) ? array_values(explode('/', ltrim($_SERVER['PATH_INFO'], '/'))) : [];
			} else {
				$this->segments = array_key_exists('path', $_GET) ? array_values(explode('/', $_GET['path'])) : [];
			}

			$this->arguments = $this->segments;
			if(count($this->arguments) > 0) {
				if(count($this->arguments) == 1) unset($this->arguments[0]);
				if(count($this->arguments) >= 2) unset($this->arguments[0], $this->arguments[1]);
				
				$this->arguments = array_values($this->arguments);
			}
			
			$this->current_url = $this->getCurrentUrl();
		}
		
		
	}
	
	public function getSegment($n) { return array_key_exists((int) $n, $this->segments) ? $this->segments[(int) $n] : null; }
	public function getSegments() { return $this->segments; }
	public function getSegmentsCount() { return count($this->segments); }
	
	public function getArgument($n) { return array_key_exists((int) $n, $this->arguments) ? $this->arguments[(int) $n] : null; }
	public function getArguments() { return $this->arguments; }
	public function getArgumentsCount() { return count($this->arguments); }
	
	public function getBaseUrl() {
		if($this->base_url) return $this->base_url;
		$port = (int) $_SERVER['SERVER_PORT'];
		$hostname = $_SERVER['HTTP_HOST'];
		$is_https = (bool) (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
		
		$http_part = $is_https ? 's' : '';
		$port_part = ($port == 80 && !$is_https) || ($port == 443 && $is_https) ? '' : ':' . $port;
		$hostname_part = $hostname . $port_part;
		
		return sprintf('http%s://%s%s', $http_part, $hostname_part, $this->base_path);
	}
	
	public function getCurrentUrl()
    {
		$query_arr = [];
		foreach($_GET as $k => $v) {
			if($k === 'path' || is_array($v)) continue;
			$query_arr[] = $k . (strlen($v) > 0 ? '=' . urlencode($v) : '');
		}
		$query_string = (count($query_arr) > 0 ? '?' : '') . implode('&', $query_arr);
        return  $this->base_url . implode('/', $this->getSegments()) . $query_string; die();
    }
	
	public function getUrl($uri) {
		$url = $this->getBaseUrl() . $uri;
		$url = preg_replace('/([^:])(\/{2,})/', '$1/', $url);
		return $url;
	}
	
	private function getBasePath() {
		if($this->base_path) return $this->base_path;
		$base_path =  array_key_exists('PATH_INFO', $_SERVER) ? substr($_SERVER['PHP_SELF'], 0, -strlen($_SERVER['PATH_INFO'])) : $_SERVER['PHP_SELF'];
		
		if(basename($base_path) === 'index.php') $base_path = substr($base_path,0,-strlen('index.php'));
		
		if(substr($base_path,-1) != '/') $base_path .= '/';
		
		return $base_path;
	}

}