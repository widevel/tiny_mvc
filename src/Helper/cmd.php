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

function call_console_cmd(string $name, array $arguments = [], bool $background = true, bool $serialize_arguments = true) {
	if(CLI_CONSOLE || !$background) {
		TinyMvc\Service\Console::callConsoleClass($name, $arguments);
		return;
	}
	$cmd = cmd_get_php_bin_path();
	log_d("PHP Bin Path: " . $cmd, "TinyMvc.Helper.Cmd");
	$cmd .= ' ' . service('path')->php . 'console.php ' . escapeshellarg($name);
	if(count($arguments) > 0 && $serialize_arguments) {
		$cmd .= ' ' . escapeshellarg('serialized');
		$cmd .= ' ' . escapeshellarg(base64_encode(gzdeflate(serialize($arguments), 9)));
	}
	
	if(!$serialize_arguments) {
		foreach($arguments as $argument) $cmd .= ' ' . escapeshellarg($argument);
	}
	
	if($background) $cmd .= cmd_get_background_str();
	log_d("CMD: " . $cmd, "TinyMvc.Helper.Cmd");
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

function cmd_pass_arguments(array $arguments) {
	$session_hash = generate_random_str(32);
	$session_key = 'cmd_' . $session_hash;
	php_session_set($session_key, $arguments);
	return $session_hash;
}

function cmd_unserialize_arguments(string $serialized) {
	$data = _unserialize(gzinflate(base64_decode($serialized)));
	if($data === UNSERIALIZE_ERR) throw new \Exception(sprintf('Unable to unserialize cmd argument %s.', $serialized));
	return $data;
}