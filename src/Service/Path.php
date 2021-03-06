<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
namespace TinyMvc\Service;

class Path {
	
	public $base, $template;
	
	public function __construct() {
		$this->base = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
		$this->php = $this->base . 'php' . DIRECTORY_SEPARATOR;
		$this->template = $this->base . 'template' . DIRECTORY_SEPARATOR;
		$this->logs = $this->base . 'logs' . DIRECTORY_SEPARATOR;
		$this->config = $this->base . 'config' . DIRECTORY_SEPARATOR;
		$this->cache = $this->base . 'cache' . DIRECTORY_SEPARATOR;
		$this->tmp = $this->base . 'tmp' . DIRECTORY_SEPARATOR;
		$this->lang = $this->base . 'lang' . DIRECTORY_SEPARATOR;

		log_i("Paths", "TinyMvc.Service.Path", [], get_object_vars($this));
	}
	
	

}