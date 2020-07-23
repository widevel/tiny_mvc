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
use TinyMvc\InputData\FormField;
use TinyMvc\InputData\Element;
class Form {
	private $name;
	private $fields = [];
	private $method = 'POST';
	
	public function getName() :string { return $this->name; }
	public function getMethod() :string { return $this->method; }
	public function getField(string $name) { return array_key_exists($name, $this->fields) ? $this->fields[$name] : null; }
	public function getFields() :array { return $this->fields; }
	
	public function setName(string $name) { $this->name = $name; }
	public function setMethod(string $method) { $this->method = $method; }
	public function addField(string $name, array $options) :Form {
		$field = new FormField($options, $name);
		$this->fields[$name] = $field;
		return $this;
	}
	
}