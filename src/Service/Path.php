<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */
namespace TinyMvc\Service;

class Path {
	
	public $base, $template;
	
	public function __construct() {
		$this->base = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
		$this->template = $this->base . 'template' . DIRECTORY_SEPARATOR;
		$this->logs = $this->base . 'logs' . DIRECTORY_SEPARATOR;
	}
	
	

}