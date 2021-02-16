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

use Widevel\SmartlogClient\Log;

class SmartLog extends Log {
	
	public function __construct() {
		$config = get_config('smartlog');
		
		parent::__construct();

		$sender_method = $config['sender_method'];

		$this->setSenderMethod($sender_method);

		if($sender_method === 'http') {
			$this
			->setUnsendedLogsPath(service('path')->base . 'smartlog')
			->setServerUrl($config['server_url'])
			->setSslVerify($config['ssl_verify']);
		}
		if($sender_method === 'cmd') $this->setServerCmdPath($config['server_cmd_path']);
		
		$this->sendPendingLogs();

	}
	
}