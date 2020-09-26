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
use TinyMvc\Service\Controller;

class Template {
	public $html;
	public function render(string $view = null, array $data = [], bool $return = false) {
		if($view === null && service_exists('route')) {
			$current_route = service('route')->getCurrent();
			if($current_route === null) throw new \Exception('Current router is undefined.');
			$view = $current_route->getView();
			if($view === null) throw new \Exception('View not specified in render() and route view is undefined.');
		} else if($view === null) $view = Page::$page_name . DIRECTORY_SEPARATOR . Page::$action_name . '.phtml';
		$view_path = $this->getViewPath($view);
		if($view_path === false) throw new \Exception(sprintf('View %s not exists', $view));
		$html = self::renderHtml($view_path, $data);
		if($return === true) return $html;
		
		$this->html = $html;

		return $this;
	}
	
	public function getViewPath(string $view) {
		return realpath(service('path')->template . implode(DIRECTORY_SEPARATOR, self::parseViewName($view)));
	}
	
	private static function renderHtml() {
		ob_start();
		foreach(func_get_arg(1) as $__var_name => $__var_value) $$__var_name = $__var_value;
		include(func_get_arg(0));
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	private static function parseViewName(string $view = null) :array {
		if($view === null) return [];
		$separator = DIRECTORY_SEPARATOR;
		if(stripos($view, chr(47)) !== false) $separator = chr(47);
		if(stripos($view, chr(92)) !== false) $separator = chr(92);
		
		return explode($separator, $view);
	}
}