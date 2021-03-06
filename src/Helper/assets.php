<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
function getAsset(string $file, array $params = [], string $path = null) :string {
	$file = ltrim($file, '/');
	$path = $path !== null ? $path : $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . service('url')->base_path . $file;
	if(is_file($path)) $params['t'] = filemtime($path);
	return add_params_url((!is_url($file) ? service('url')->base_url : '') . $file, $params);
}