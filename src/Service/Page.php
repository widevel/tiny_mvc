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

class Page {
	
	public static $page_name, $action_name;
	
	public function __construct() {
		$response_service = service('response');
		
		$route_service = service('route');
		
		$page_segment = $route_service->page_segment;
		$action_segment = $route_service->action_segment;
		
		$actionArguments = [];
		
		if($route_service->isEnabled()) {
			$relativeClassName = $route_service->relativeClassName;
			$actionName = $route_service->actionName;
			$actionArguments = $route_service->actionArguments;
		} else {
			$page = self::getPageNameFromSegment($page_segment);
			$actionName = self::getActionNameFromSegment($action_segment);
			log_d(sprintf('Request: %s/%s', $page, $actionName), 'TinyMvcServicePage');
			
			$relativeClassName = $page;
		}
		
		$className = sprintf('\%s\Page\\%s', BUNDLE_NAME, $relativeClassName);
		
		if(!class_exists($className)) {
			log_d(sprintf('404: Class %s not exists', $className), 'TinyMvcServicePage');
			$response_service->setCode(404);
			return;
		}
		
		$instance = new $className;
		
		
		
		if(!method_exists($instance, $actionName)) {
			log_d(sprintf('404: Method %s:%s not exists', $className, $actionName), 'TinyMvcServicePage');
			$response_service->setCode(404);
			return;
		}
		
		$return = call_user_func_array([$instance, $actionName], $actionArguments);
		
		$return_classes = [
			\TinyMvc\Service\Response::class,
			\TinyMvc\Service\ResponseJson::class,
			\TinyMvc\Service\Template::class
		];
		if($return !== null) {
			log_d(sprintf('Page return: %s', get_class($return)), 'TinyMvcServicePage');
			if(!(is_object($return) && in_array(get_class($return), $return_classes))) throw new \Exception(sprintf('Page %s->%s() has be invalid return', $className, $actionName));
		
			$response_service->mergeData($return);
		} else log_d('Page return: null', 'TinyMvcServicePage');
		
		
	}
	
	private static function getPageNameFromSegment(string $page_segment = null) {
		if($page_segment === null) $page_segment = 'home';
		self::$page_name = $page_segment;
		return ucfirst($page_segment);
	}
	
	private static function getActionNameFromSegment(string $action_segment = null) {
		if($action_segment === null) $action_segment = 'index';
		self::$action_name = $action_segment;
		$action_segment .= 'Action';
		
		return $action_segment;
	}
}