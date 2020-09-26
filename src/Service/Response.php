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
	
	const LOG_TAG = 'TinyMvc.Service_Response';
	
	private $body;
	private $headers = [];
	private $code = 200;
	private $redirect;
	
	public static $HTTP_RESPONSE_CODE_SETTED = false;
	
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
		log_d(self::LOG_TAG, 'Sending headers: %s', $this->headers);
		foreach($this->headers as $name => $value) header(sprintf('%s: %s', $name, $value));
	}
	
	public function redirect(string $uri = '') {
		log_d(self::LOG_TAG, 'Redirect uri: %s', $uri);
		$redirect_url = is_url($uri) ? $uri : service('url')->getUrl($uri);
		log_d(self::LOG_TAG, 'Redirect url: %s', $redirect_url);
		$this->setHeader('Location',  $redirect_url);
		return $this;
	}
	
	public function __destruct() {
		if($this->body !== null) echo $this->body;
		if(self::$HTTP_RESPONSE_CODE_SETTED) $this->code = self::$HTTP_RESPONSE_CODE_SETTED;
		log_d(self::LOG_TAG, 'HTTP code: %d', $this->code);
		if(!headers_sent()) {
			http_response_code($this->code);
			$this->sendHeaders();
		} else log_e(self::LOG_TAG, 'Headers already sended, cannot send again');
		
	}
	
	public function mergeFromResponseJson(\TinyMvc\Service\ResponseJson $class) {
		log_d(self::LOG_TAG, 'Merging from ResponseJson');
		$this->setHeader('Content-Type', 'application/json');
		$this->body = json_encode($class->getAllData());
		log_d(self::LOG_TAG, 'JSON: %s', $this->body);
	}
	
	public function mergeFromTemplate(\TinyMvc\Service\Template $class) {
		log_d(self::LOG_TAG, 'Merging from Template');
		$this->body = $class->html;
	}
	
	public function mergeData(object $class) {
		if(get_class($class) == \TinyMvc\Service\ResponseJson::class) $this->mergeFromResponseJson($class);
		if(get_class($class) == \TinyMvc\Service\Template::class) $this->mergeFromTemplate($class);
	}
}