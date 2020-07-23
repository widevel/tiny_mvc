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

class Form {
	
	private $forms = [];
	
	public function __construct() {
		
	}
	
	public function create(string $class_name) {
		$form_instance = new $class_name;
		$form_instance->populate();
		return $this;
	}
	
	public function getRequest(string $class_name) {
		$name = constant($class_name . "::FORM_NAME");
		if(!array_key_exists($name, $this->forms)) throw new \Exception(sprintf('Form %s not exists', $name));
		return $this->forms[$name];
	}
	
	public function addForm(string $name, \TinyMvc\InputData\Form $form) {
		$this->forms[$name] = $form;
	}
	
	public function getFieldValue(string $name, string $field, $default_value = null) {
		if(!array_key_exists($name, $this->forms)) throw new \Exception(sprintf('Form %s not exists', $name));
		
	}
	
}