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

class ResponseJson {
	
	const LOG_TAG = 'TinyMvc.Service_ResponseJson';
	
	private $data = [];
	
	public function setData($name, $value = null) :object {
		$this->data[$name] = $value;
		return $this;
	}
	
	public function getAllData(): array {
		log_d('json', self::LOG_TAG, [], $this->data);
		return $this->data;
	}
}