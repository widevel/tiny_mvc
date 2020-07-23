<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
namespace TinyMvc\InputData;
use TinyMvc\Library\InputData;

class FormField {
	private $name;
	private $element;
	private $request_exists = false;
	
	public function __construct(array $options, string $name) {
		$this->setName($name);
		$options['name'] = $name;
		$this->element = InputData::getElement($options);
	}
	
	public function getName() :string { return $this->name; }
	public function setName(string $name) { $this->name = $name; }
	public function setRequestExists() { $this->request_exists = true; }
	
	public function __call($name, $arguments) {
		return call_user_func_array([$this->element, $name], $arguments);
	}
	
}