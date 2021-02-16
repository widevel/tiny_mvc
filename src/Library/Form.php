<?php

namespace TinyMvc\Library;

use TinyMvc\InputData\Form as FormData;

class Form {
		
	private $form_service, $form, $source_data = [];
	
	public function __construct(string $name) {
		log_d("Form name: " . $name, "TinyMvc.Library.Form");
		$this->form_service = service('form');
		$this->form = new FormData;
		$this->form->setName($name);
		$this->form_service->addForm($name, $this->form);
	}
	
	public function addField() { return call_user_func_array([$this->form, 'addField'], func_get_args()); }
	public function setMethod() { return call_user_func_array([$this->form, 'setMethod'], func_get_args()); }
	
	public function getField(string $name) {
		$field = $this->form->getField($name);
		if($field === null) throw new \Exception(sprintf('Field %s not exists in form %s', $name, $this->form->getName()));
		return $field; 
	}
	
	public function populate() {
		log_d("Method: " . $this->form->getMethod(), "TinyMvc.Library.Form");
		switch($this->form->getMethod()) {
			case 'POST':
				$this->source_data = $_POST;
				break;
			case 'GET':
				$this->source_data = $_GET;
				break;
		}
		log_d("Input Data", "TinyMvc.Library.Form", [], self::cleanPasswordsForLogs($this->source_data));
		foreach($this->form->getFields() as $field) {
			if($this->requestExists($field->getName())) {
				$field_value = $this->getRequestData($field->getName());
				$field->setValue($field_value);
			}
		}
	}
	
	
	public function requestExists(string $name) {
		return array_key_exists($name, $this->source_data);
	}
	
	public function getRequestData(string $name) {
		return array_key_exists($name, $this->source_data) ? $this->source_data[$name] : null;
	}

	private static function cleanPasswordsForLogs(array $source_data) {
		foreach($source_data as $field => $value) {
			if($field === 'password') $source_data[$field] = str_repeat('*', 8);
		}
		return $source_data;
	}
}