<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

use Symfony\Component\Process\PhpExecutableFinder;

function call_console_cmd(string $name, $arguments = [], bool $background = true) {
	$cmd = cmd_get_php_bin_path();
	$cmd .= ' ' . service('path')->php . 'console.php ' . escapeshellarg($name);
	foreach($arguments as $argument) $cmd .= ' ' . escapeshellarg($argument);
	if($background) $cmd .= cmd_get_background_str();
	log_d('cmd:' . $cmd, 'HelperCmd');
	return shell_exec($cmd);
}

function cmd_get_php_bin_path() :string {
	if(defined('PHP_BIN_PATH')) return PHP_BIN_PATH;
	$phpFinder = new PhpExecutableFinder;
    if (!$phpPath = $phpFinder->find()) {
        throw new \Exception('The php executable could not be found, add it to your PATH environment variable and try again');
    }
	define('PHP_BIN_PATH', $phpPath);
    return $phpPath;
}

function cmd_get_background_str() {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return ' > NUL 2> NUL';
	} else {
		return ' > /dev/null 2>/dev/null &';
	}
}