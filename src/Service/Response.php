<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */
namespace TinyMvc\Service;

class Response {
	private $body;
	private $headers = [];
	private $code = 200;
	private $redirect;
	
	public function deleteHeader(string $name) {
		if(array_key_exists($name, $this->headers)) unset($this->headers[$name]);
		return $this;
	}
	public function setHeader(string $name, string $value) {
		$this->headers[$name] = $value;
		return $this;
	}
	
	public function setCode(int $code) {
		$this->code = $code;
		return $this;
	}
	
	public function setBody(string $body = null) {
		$this->body = $body;
		return $this;
	}
	
	public function sendHeaders() {
		foreach($this->headers as $name => $value) header(sprintf('%s: %s', $name, $value));
	}
	
	public function redirect(string $uri = '') {
		$this->setHeader('Location', service('url')->getBaseUrl() . $uri);
		return $this;
	}
	
	public function __destruct() {
		if($this->body !== null) echo $this->body;
		http_response_code($this->code);
		foreach($this->headers as $name => $value) header(sprintf('%s: %s', $name, $value));
	}
	
	public function mergeFromResponseJson(\TinyMvc\Service\ResponseJson $class) {
		$this->setHeader('Content-Type', 'application/json');
		$this->body = json_encode($class->getAllData());
	}
	
	public function mergeFromTemplate(\TinyMvc\Service\Template $class) {
		$this->body = $class->html;
	}
	
	public function mergeData(object $class) {
		if(get_class($class) == \TinyMvc\Service\ResponseJson::class) $this->mergeFromResponseJson($class);
		if(get_class($class) == \TinyMvc\Service\Template::class) $this->mergeFromTemplate($class);
	}
}