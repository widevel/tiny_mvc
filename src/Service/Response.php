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
		log_d(sprintf('Sending headers: %s', json_encode($this->headers)), 'ServiceResponse');
		foreach($this->headers as $name => $value) header(sprintf('%s: %s', $name, $value));
	}
	
	public function redirect(string $uri = '') {
		log_d(sprintf('Redirect uri: %s', $uri), 'ServiceResponse');
		$this->setHeader('Location', service('url')->getBaseUrl() . $uri);
		return $this;
	}
	
	public function __destruct() {
		if($this->body !== null) echo $this->body;
		log_d(sprintf('HTTP code: %d', $this->code), 'ServiceResponse');
		http_response_code($this->code);
		$this->sendHeaders();
	}
	
	public function mergeFromResponseJson(\TinyMvc\Service\ResponseJson $class) {
		log_d('Merging from ResponseJson', 'ServiceResponse');
		$this->setHeader('Content-Type', 'application/json');
		$this->body = json_encode($class->getAllData());
	}
	
	public function mergeFromTemplate(\TinyMvc\Service\Template $class) {
		log_d('Merging from Template', 'ServiceResponse');
		$this->body = $class->html;
	}
	
	public function mergeData(object $class) {
		if(get_class($class) == \TinyMvc\Service\ResponseJson::class) $this->mergeFromResponseJson($class);
		if(get_class($class) == \TinyMvc\Service\Template::class) $this->mergeFromTemplate($class);
	}
}