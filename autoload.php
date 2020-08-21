<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
function load_php_files($directory) {
	if(is_dir($directory)) {
		if(substr($directory,-1) != DIRECTORY_SEPARATOR) $directory .= DIRECTORY_SEPARATOR;
		$scan = scandir($directory);
		foreach($scan as $file) {
			if(in_array($file, ['.', '..'])) continue;
			$abs_path = realpath($directory . $file);
			if(is_dir($abs_path)) {
				load_php_files($abs_path);
			} else if(is_file($abs_path)) {
				if(pathinfo($file, PATHINFO_EXTENSION) === 'php') {
					if(!in_array($abs_path, get_included_files())) include_once($abs_path);
				}
			}
		}
	}
}