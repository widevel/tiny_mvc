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

class Database {
	
	const LOG_TAG = 'TinyMvc.Service_Database';
	
	const DEFAULT_PORT = 3306;
	
	public $link, $lastQuery;
	
	public $modelsInstances = [], $models = [];
	
	public function __construct() {
		$database_models_config = get_config('database_models');
		
		if(!is_array($database_models_config)) throw new \Exception('unable to load database_models.yaml config');
		
		$this->models = $database_models_config;
		
		$database_config = get_config('database');
		
		if(!is_array($database_config)) throw new \Exception('unable to load database.yaml config');
		
		$this->hostname = array_key_exists('hostname', $database_config) ? $database_config['hostname'] : null;
		$this->port = array_key_exists('port', $database_config) ? $database_config['port'] : self::DEFAULT_PORT;
		$this->user = array_key_exists('user', $database_config) ? $database_config['user'] : null;
		$this->pass = array_key_exists('pass', $database_config) ? $database_config['pass'] : null;
		$this->db = array_key_exists('db', $database_config) ? $database_config['db'] : null;
		
		if(!$this->hostname) throw new \Exception('hostname var is missing in config file');
		if(!$this->user) throw new \Exception('user var is missing in config file');
		if(!$this->pass) throw new \Exception('pass var is missing in config file');
		if(!$this->db) throw new \Exception('db var is missing in config file');
	}
	
	public function getModel($name) {
		if($this->link === null) $this->connect();
		if(array_key_exists($name, $this->modelsInstances)) return $this->modelsInstances[$name];
		if(!array_key_exists($name, $this->models)) throw new \Exception(sprintf('model %s not defined', $name));
		
		$modelClassName = $this->models[$name];
		
		$this->modelsInstances[$name] = new $modelClassName($this);
		
		return $this->modelsInstances[$name];
	}
	
	public function connect() {
		if(is_object($this->link)) return;
		log_d(self::LOG_TAG, 'Connecting to mysql: HOSTNAME %s USER %s DB %s PORT %d', $this->hostname, $this->user, $this->db, $this->port);
		$link = new \mysqli($this->hostname, $this->user, $this->pass, $this->db, $this->port);
		$link->set_charset("utf8mb4");
		
		if ($link->connect_errno) {
			throw new Exception('Connection failed ' . $link->connect_error);
		}
		
		$this->link = $link;
	}
	
	public function selectRows(string $query, array $binds = []) {
		$this->lastQuery = null;
		log_d(self::LOG_TAG, 'Query: %s Binds: %s', $query, json_encode($binds));
		$query = $this->bindsQuery($query, $binds);
		log_d(self::LOG_TAG, 'Runnable Query: %s', $query);
		$this->lastQuery = $query;
		$rows = [];
		if ($result = $this->link->query($query)) {
			if($result->num_rows > 0) {
				while($obj = $result->fetch_object()) {
					$rows[] = $obj;
				}
				$result->close();
				
			}
			
		}

		if($this->link->error) throw new \Exception($query . ' - ' . $this->link->error);
		log_d(self::LOG_TAG, 'Rows results: %d', count($rows));
		return $rows;
	}
	
	public function selectOneRow(string $query, array $binds = []) {
		$this->lastQuery = null;
		log_d(self::LOG_TAG, 'Query: %s Binds: %s', $query, json_encode($binds));
		$query = $this->bindsQuery($query, $binds);
		log_d(self::LOG_TAG, 'Runnable Query: %s', $query);
		$this->lastQuery = $query;
		if ($result = $this->link->query($query)) {
			if($result->num_rows > 0) {
				$obj = $result->fetch_object();
				log_d(self::LOG_TAG, 'Response: %s', json_encode($obj));
				$result->close();
				return $obj;
			}
			
		}

		if($this->link->error) throw new \Exception($query . ' - ' . $this->link->error);
		log_d(self::LOG_TAG, 'Response: null');
		return null;
	}
	
	public function simpleQuery(string $query, array $binds = []) {
		$this->lastQuery = null;
		log_d(self::LOG_TAG, 'Query: %s Binds: %s', $query, json_encode($binds));
		$query = $this->bindsQuery($query, $binds);
		log_d(self::LOG_TAG, 'Runnable Query: %s', $query);
		$this->lastQuery = $query;
		$this->link->query($query);
		if($this->link->error) throw new \Exception($query . ' - ' . $this->link->error);
		
		return null;
	}
	
	
	public function update(string $table, array $data, string $where_str, array $binds = []) {
		
		$set = [];
		
		foreach($data as $field => $value) {
			$bind_mask = '%s';
			if(is_int($value)) $bind_mask = '%d';
			if(is_float($value)) $bind_mask = '%f';
			if(is_double($value)) $bind_mask = '%f';
			if(is_bool($value)) {
				$data[$field] = $value?1:0;
				$bind_mask = '%d';
			}
			if(is_null($value)) {
				unset($data[$field]);
				$bind_mask = 'NULL';
			}
			$set[] = sprintf('`%s` = %s', $field, $bind_mask);
		}
		
		$sql = sprintf('UPDATE %s SET %s WHERE %s;', $table, implode(', ', $set), $where_str);
		
		$binds = array_merge(array_values($data), $binds);
		
		return $this->simpleQuery($sql, $binds);
	}
		
	public function insert($table, $data, $ignore = false) {
		$fields = implode('`,`', array_keys($data));
		$values_part = implode(',', array_fill(0,count($data), '?'));
		$sql = sprintf("INSERT%s INTO `%s` (`%s`) VALUES (%s)", ($ignore ? ' IGNORE' : ''), $table, $fields, $values_part);
		log_d(self::LOG_TAG, "INSERT SQL: %s\nWith data: %s", $sql, json_encode(array_values($data)));
		$stmt = $this->link->prepare($sql);
		if($stmt === false) throw new \Exception($sql . ' - ' . $this->link->error);
		$bind_mask = '';
		foreach($data as $value) {
			if(is_int($value)) $bind_mask .= 'i';
			if(is_double($value)) $bind_mask .= 'd';
			if(is_bool($value)) $bind_mask .= 'd';
			if(is_string($value)) $bind_mask .= 's';
			if(is_null($value)) $bind_mask .= 's';
		}
		
		$stmt = call_user_func_array([$this, 'bind_param_bridge'], array_merge([$stmt, $bind_mask], array_values($data)));
		$stmt_executed = (!$stmt->execute()) ? false : true;
		if($stmt->error || $stmt_executed === false) throw new \Exception($stmt->error);
		$stmt->free_result();
		$stmt->close();
		return $stmt_executed;
	}
	
	public function bind_param_bridge() {
		$stmt = func_get_arg(0);
		$bind_mask = func_get_arg(1);
		$vars_list = [];
		for($i=2;$i<func_num_args();$i++) {
			$var_name = 'param_' . $i;
			$vars_list[] = '$' . $var_name;
			$$var_name = func_get_arg($i);
		}
		$code = '$stmt->bind_param($bind_mask, '.implode(',', $vars_list).');';
		eval($code);
		return $stmt;
	}
	
	public function bindsQuery(string $query, array $binds) :string {
		$binds = array_map([$this, 'escapeBinds'], $binds);
		return call_user_func_array('sprintf', array_merge([$query], $binds));
	}
	
	public function escapeBinds($v) {
		if(is_int($v)) return $v;
		if(is_double($v)) return $v;
		if(is_float($v)) return $v;
		if(is_string($v)) return "'" . $this->link->real_escape_string($v) . "'";
		if(is_null($v)) return 'null';
		throw new \Exception('%s type is not allowed in escapeBinds func', gettype($v));
	}
	
	public function getLastQuery() {
		return $this->lastQuery;
	}
}