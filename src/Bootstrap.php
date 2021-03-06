<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
use TinyMvc\Service\Response;
use TinyMvc\Service\ResponseJson;
use TinyMvc\Service\Path;
use TinyMvc\Service\LogLocal;
use TinyMvc\Service\Config;
use TinyMvc\Service\Url;
use TinyMvc\Service\Controller;
use TinyMvc\Service\Console;
use TinyMvc\Service\Request;
use TinyMvc\Service\CacheRunTime;

class TinyMvcBootstrap {
	
	const SERVICE_CLASS = [
		'path' => Path::class,
		'log' => LogLocal::class,
		'config' => Config::class,
	];
	
	private $service_instance = [];
	
	public function load_services() {
		
		$services_arr = self::SERVICE_CLASS;
		$services_arr['cache_runtime'] = CacheRunTime::class;
		
		if(!CLI_CONSOLE) {
			$services_arr['response'] = Response::class;
			$services_arr['response_json'] = ResponseJson::class;
			$services_arr['url'] = Url::class;
			$services_arr['request'] = Request::class;
		}
		
		$services_yaml_file = realpath(__DIR__ . '/../../../config/services'.(CLI_CONSOLE ? '_cmd' : '').'.yaml');
		if($services_yaml_file !== false) {
			$user_services = yaml_parse_file($services_yaml_file);
			if(is_array($user_services)) {
				foreach($user_services as $service_name => $class_name) {
					$services_arr[$service_name] = $class_name;
				}
			}
		}
		
		if(!CLI_CONSOLE) $services_arr['controller'] = Controller::class;
		if(CLI_CONSOLE) $services_arr['console'] = Console::class;
		
		foreach($services_arr as $service_name => $class_name) {
			if(!class_exists($class_name)) throw new \Exception(sprintf('Unable to load service %s class %s not exists', $service_name, $class_name));
			if(service_exists($service_name)) continue;
			$this->service_load($service_name, $class_name);
		}
	}
	
	public function service_load(string $service_name, string $class_name) {
		$this->service_instance[$service_name] = new $class_name;
	}
	
	public function get_service(string $service_name) :object {
		if(!array_key_exists($service_name, $this->service_instance)) {
			throw new \Exception(sprintf('Service %s not exists', $service_name));
		}
		return $this->service_instance[$service_name];
	}
	
	public function service_exists(string $service_name) :bool {
		return array_key_exists($service_name, $this->service_instance);
	}
}

function service_load(string $service_name, string $class_name) {
	global $bootstrap_class;
	$bootstrap_class->service_load($service_name, $class_name);
}

function service_exists(string $service_name) :bool {
	global $bootstrap_class;
	return (is_object($bootstrap_class)) ? $bootstrap_class->service_exists($service_name) : false;
}

function service(string $service_name) :object {
	global $bootstrap_class;
	return $bootstrap_class->get_service($service_name);
}