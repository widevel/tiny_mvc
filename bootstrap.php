<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */
$composer_autoload_file = realpath('../composer/vendor/autoload.php');

if($composer_autoload_file !== false) require_once $composer_autoload_file;
require_once 'autoload.php';

$bundlename_file = realpath(__DIR__ . '/../bundle.name');
if($bundlename_file === false) throw new Exception('File bundle.name not exists');
define('BUNDLE_NAME',  file_get_contents($bundlename_file));

load_php_files('src');
load_php_files(__DIR__ . '/../' . BUNDLE_NAME);

$bootstrap_class = new TinyMvcBootstrap;
$bootstrap_class->load_services();
