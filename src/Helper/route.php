<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
function route_url(string $name, array $params = [], string $sufix = null, bool $abs_url = true) {
	return route_uri($name, $params, $sufix, true);
}

function route_uri(string $name, array $params = [], string $sufix = null, bool $abs_url = false) {
	$route = service('route')->get($name);
	if($route === null) throw new \Exception(sprintf('Route %s not exists', $name));
	$uri = $route->getUri();
	if(substr($uri,-1) != '/' && (count($params) > 0 || $sufix !== null)) $uri .= '/';
	if(count($params) > 0) $uri .= implode('/', $params);
	if($sufix !== null) $uri .= $sufix;
	return $abs_url ? service('url')->getUrl($uri) : $uri;
}