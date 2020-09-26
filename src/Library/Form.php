<?php

namespace TinyMvc\Library;

use TinyMvc\InputData\Form as FormData;

class Form {
	
	const LOG_TAG = 'TinyMvc.Library_Form';
	
	private $form_service, $form, $source_data = [];
	
	public function __construct(string $name) {
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
		log_d(self::LOG_TAG, 'Method %s', $this->form->getMethod());
		switch($this->form->getMethod()) {
			case 'POST':
				$this->source_data = $_POST;
				break;
			case 'GET':
				$this->source_data = $_GET;
				break;
		}
		foreach($this->form->getFields() as $field) {
			if($this->requestExists($field->getName())) {
				$field_value = $this->getRequestData($field->getName());
				log_d(self::LOG_TAG, 'Field %s Value %s', $field->getName(), $field_value);
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
}