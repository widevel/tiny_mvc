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

use TinyMvc\InputData\Route as RouteData;
use TinyMvc\Library\InputData;

class Route {
	
	private $enabled = false, $routes = [], $routes_by_uri = [], $current;
	
	public function __construct() {
		$config = service('config')->get('route', false);
		
		$this->enabled = (is_array($config) && array_key_exists('auto_route', $config) && $config['auto_route'] === true);
		
		if(!$this->isEnabled()) return;
		
		if(is_array($config) && array_key_exists('routes', $config) && is_array($config['routes'])) $this->routes = $config['routes'];
		
		foreach($this->routes as $name => $row) {
			
			if(!array_key_exists('class', $row)) throw new \Exception(sprintf('"class" property is required in route %s.', $name));
			
			$route = new RouteData;
			$route->setName($name);
			if(array_key_exists('uri', $row)) $route->setUri(rtrim($row['uri'], '/'));
			if(array_key_exists('view', $row)) $route->setView($row['view']);
			
			if($route->isDefault() && $route->getView() === null) throw new \Exception(sprintf('"view" property is required in route %s.', $route->getName()));
			
			if(!$route->isDefault() && $route->getUri() === null) throw new \Exception(sprintf('Uri property in route %s is required beacuse is not default.', $route->getName()));
			
			$route->setClass($row['class']);
			
			if(array_key_exists('arguments', $row) && is_array($row['arguments'])) {
				foreach($row['arguments'] as $argument_name => $argument_row) {
					$route->addArgument($argument_name, InputData::getElement($argument_row));
				}
			}
			
			$this->routes[$route->getName()] = $route;
			
			if($route->getUri() !== null) $this->routes_by_uri[$route->getUri()] = $route->getName();
			
			
		}
		
	}
	
	
	public function isEnabled() :bool { return $this->enabled; }
	public function setCurrent(string $name) { $this->current = $this->get($name); }
	public function getCurrent() { return $this->current; }
	public function getByUri(string $uri) { $uri = rtrim($uri, '/'); return array_key_exists($uri, $this->routes_by_uri) && $this->get($this->routes_by_uri[$uri]) !== null ? $this->get($this->routes_by_uri[$uri]) : null; }
	public function get(string $name) {return array_key_exists($name, $this->routes) ? $this->routes[$name] : null; }
	public function getDefault() { return array_key_exists('default', $this->routes) ? $this->routes['default'] : null; }
	
	
	
}