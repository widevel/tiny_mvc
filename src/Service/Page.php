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
		$url_service = service('url');
		$response_service = service('response');
		$page = $url_service->getSegment(0);
		$action = $url_service->getSegment(1);
		
		if($page === null) $page = 'home';
		if($action === null) $action = 'index';
		
		log_d(sprintf('[Service Page] Request: %s/%s', $page, $action));
		
		$page = strtolower($page);
		self::$page_name = $page;
		$page = ucfirst($page);
		
		$action = strtolower($action);
		self::$action_name = $action;
		$action .= 'Action';
		
		$className = sprintf('\%s\Page\\%s', BUNDLE_NAME, $page);
		
		if(!class_exists($className)) {
			log_d(sprintf('[Service Page] 404: Class %s not exists', $className));
			$response_service->setCode(404);
			return;
		}
		
		$instance = new $className;
		
		if(!method_exists($instance, $action)) {
			log_d(sprintf('[Service Page] 404: Method %s:%s not exists', $className, $action));
			$response_service->setCode(404);
			return;
		}
		
		$return = call_user_func([$instance, $action]);
		
		$return_classes = [
			\TinyMvc\Service\Response::class,
			\TinyMvc\Service\ResponseJson::class,
			\TinyMvc\Service\Template::class
		];
		if($return !== null) {
			log_d(sprintf('[Service Page] Page return: %s', get_class($return)));
			if(!(is_object($return) && in_array(get_class($return), $return_classes))) throw new \Exception(sprintf('Page %s->%s() has be invalid return', $className, $action));
		
			$response_service->mergeData($return);
		} else log_d('[Service Page] Page return: null');
		
		
	}
}