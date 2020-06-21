<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */
namespace TinyMvc\Service;
use TinyMvc\Service\Page;

class Template {
	public $html;
	public function render(string $view = null, array $data = [], bool $return = false) {
		if($view === null) $view = Page::$page_name . DIRECTORY_SEPARATOR . Page::$action_name . '.html';
		$view_path = service('path')->template . implode(DIRECTORY_SEPARATOR, self::parseViewName($view));
		if(!is_file($view_path)) throw new \Exception(sprintf('View %s not exists', $view_path));
		$html = self::renderHtml($view_path, $data);
		if($return === true) return $html;
		
		$this->html = $html;
		
		return $this;
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