<?php

if(count($argv) <= 1) {
	throw new Exception('no argument specified');
}

define('CLASS_CONSOLE', $argv[1]);
define('CLI_CONSOLE', 1);

require_once 'bootstrap.php';