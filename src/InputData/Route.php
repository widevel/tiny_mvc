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

class Route {
	private $name;
	private $uri;
	private $class;
	private $view;
	private $arguments = [];
	
	public function getName() :string { return $this->name; }
	public function getUri() { return $this->uri !== null ? $this->uri : null; }
	public function getClass() :string { return $this->class; }
	public function getView() { return $this->view !== null ? $this->view : null; }
	public function getArguments() :array { return $this->arguments; }
	public function isDefault() :bool { return $this->name === 'default'; }
	
	public function setName(string $name) { $this->name = $name; }
	public function setUri(string $uri) { $this->uri = $uri; }
	public function setClass(string $class) { $this->class = $class; }
	public function setView(string $view) { $this->view = $view; }
	public function addArgument(string $name, \TinyMvc\InputData\Element $element) {
		$element->setName($name);
		$this->arguments[$name] = $element;
	}
	
}