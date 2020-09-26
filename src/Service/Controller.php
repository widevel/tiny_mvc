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

class Controller {
	
	const LOG_TAG = 'TinyMvc.Service_Controller';
	
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
			log_d(self::LOG_TAG, 'Request: %s/%s', $page, $actionName);
			
			$relativeClassName = $page;
		}
		
		$className = sprintf('\%s\Controller\\%s', BUNDLE_NAME, $relativeClassName);
		
		if(!class_exists($className)) {
			log_d(self::LOG_TAG, '404: Class %s not exists', $className);
			$response_service->setCode(404);
			return;
		}
		
		$instance = new $className;
		
		
		
		if(!method_exists($instance, $actionName)) {
			log_d(self::LOG_TAG, '404: Method %s:%s not exists', $className, $actionName);
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
			log_d(self::LOG_TAG, 'Page return: %s', get_class($return));
			if(!(is_object($return) && in_array(get_class($return), $return_classes))) throw new \Exception(sprintf('Page %s->%s() has be invalid return', $className, $actionName));
		
			$response_service->mergeData($return);
		} else log_d(self::LOG_TAG, 'Page return: null');
		
		
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