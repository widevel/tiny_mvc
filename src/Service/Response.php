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
	
	private $response_json;
	
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
		log_d('Sending headers', self::LOG_TAG, [], $this->headers);
		foreach($this->headers as $name => $value) header(sprintf('%s: %s', $name, $value));
	}
	
	public function redirect(string $uri = '') {
		log_d(sprintf('Redirect uri: %s', $uri), self::LOG_TAG);
		$redirect_url = is_url($uri) ? $uri : service('url')->getUrl($uri);
		log_d(sprintf('Redirect url: %s', $redirect_url), self::LOG_TAG);
		$this->setHeader('Location',  $redirect_url);
		return $this;
	}
	
	public function __destruct() {
		if($this->body !== null) echo $this->body;
		if(self::$HTTP_RESPONSE_CODE_SETTED) $this->code = self::$HTTP_RESPONSE_CODE_SETTED;
		log_d(sprintf('HTTP code: %d', $this->code), self::LOG_TAG);
		if(!headers_sent()) {
			http_response_code($this->code);
			$this->sendHeaders();
		}
		//else log_e(self::LOG_TAG, 'Headers already sended, cannot send again');
		
	}
	
	public function getResponseJson() :?object { return $this->response_json; }
	
	public function mergeFromResponseJson(\TinyMvc\Service\ResponseJson $class) {
		$this->response_json = $class;
		log_d('Merging from ResponseJson', self::LOG_TAG);
		$this->setHeader('Content-Type', 'application/json');
		$this->body = json_encode($class->getAllData());
		log_d('JSON: ' . $this->body, self::LOG_TAG);
	}
	
	public function mergeFromTemplate(\TinyMvc\Service\Template $class) {
		log_d('Merging from Template', self::LOG_TAG);
		$this->body = $class->html;
	}
	
	public function mergeData(object $class) {
		if(get_class($class) == \TinyMvc\Service\ResponseJson::class) $this->mergeFromResponseJson($class);
		if(get_class($class) == \TinyMvc\Service\Template::class) $this->mergeFromTemplate($class);
	}
}