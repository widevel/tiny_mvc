<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function lang_get(string $name) {
	$tag = 'default';
	$split = explode('.', $name);
	if(count($split) > 1) {
		$tag = $split[0];
		$split = array_slice($split, 1);
		$name = implode('.', $split);
	}
	
	return service('lang')->get($tag, $name);
}