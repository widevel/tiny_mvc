<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */
function getAsset($file) :string {
	$file = ltrim($file, '/');
	$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . service('url')->base_path . $file;
	return service('url')->base_url . ($file . '?' . filemtime($path));
}