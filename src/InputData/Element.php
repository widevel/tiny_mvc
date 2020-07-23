<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
namespace TinyMvc\InputData;

class Element {
	private $name;
	private $cast = 'string';
	private $max_length = 0;
	private $array_max_elements = 0;
	private $original_value;
	private $default_value;
	private $value;
	private $map_func = [];
	private $preg_replace = false;
	private $value_setted = false;
	private $double_decimals = 2;
	private $array_separator = ',';
	
	public function getName() :string { return $this->name; }
	public function getCast() { return $this->cast !== null ? $this->cast : null; }
	public function getMaxLength() { return $this->max_length !== null ? $this->max_length : null; }
	public function getArrayMaxElements() { return $this->array_max_elements !== null ? $this->array_max_elements : null; }
	public function getDoubleDecimals() { return $this->double_decimals !== null ? $this->double_decimals : null; }
	public function getArraySeparator() { return $this->array_separator !== null ? $this->array_separator : null; }
	public function getValue() { return !$this->isEmpty() ? $this->formatValue($this->value, $this->cast) : ($this->value_setted === false ? $this->default_value : null); }
	public function getOriginalValue() { return $original_value; }
	public function isEmpty() { return $this->value === null || $this->value !== null || ($this->value !== null && $this->cast == 'array' && count($this->getValue() > 0)) ? false : true; }
	
	public function setName(string $name) { $this->name = $name; }
	public function setCast(string $cast) { $this->cast = $cast; }
	public function setMaxLength(int $max_length) { $this->max_length = $max_length; }
	public function setArrayMaxElements(int $array_max_elements) { $this->array_max_elements = $array_max_elements; }
	public function setMapFunc(string $map_func) { $this->map_func = explode(',', $map_func); }
	public function setPregReplace(string $preg_replace) { $this->preg_replace = $preg_replace; }
	public function setDoubleDecimals(int $double_decimals) { $this->double_decimals = $double_decimals; }
	public function setArraySeparator(string $array_separator) { $this->array_separator = $array_separator; }
	public function setValue($value) { $this->value_setted = true; $this->original_value = $value; $this->value = $value; }
	public function setDefaultValue($default_value) { $this->default_value = $default_value; }
	
	private function formatValue($value, string $cast = null) {
		$value = $this->castValue($value, $cast);
		if($cast === null) {
			return $this->formatValue($value, gettype($value));
		}
		
		if($cast == 'base64') $cast = 'string';
		
		if($cast == 'string' && count($this->map_func) > 0) foreach($this->map_func as $func) $value = call_user_func_array($func, [$value]);
		if($cast == 'string' && $this->preg_replace !== false) $value = preg_replace($this->preg_replace, "", $value);
		if($cast == 'string' && $this->max_length > 0 && strlen($value) > $this->max_length) $value = substr($value, 0, $this->max_length);
		if($cast == 'string' && strlen($value) === 0) $value = null;
		if($cast == 'array') return ($this->array_max_elements > 0 ? array_slice($this->formatArr($value),0,$this->array_max_elements) : $this->formatArr($value));
		return $value;
		
	}
	
	private function formatArr(array $arr) {
		foreach($arr as $k => $v) $arr[$k] = $this->formatValue($v, 'string');
		return $arr;
	}
	
	private function castValue($value, string $cast = null) {
		if($value === null) return null;
		switch($cast) {
			case 'integer':
				return (int) $value;
				break;
			case 'bool':
			case 'boolean':
				return (boolean) $value;
				break;
			case 'string':
				return (string) $value;
				break;
				
			case 'double':
			case 'float':
				return self::stringToDouble($value, $this->double_decimals);
				break;
				
			case 'array':
				return self::stringToArray($value, $this->array_separator);
				break;
				
			case 'base64':
				return base64_decode($value);
				break;
		}
		
		return $value;
	}
	
	private static function stringToDouble(string $str, int $decimals) {
		$str = str_replace(",", ".", preg_replace('/[^0-9.,-]+/', '', (string) $str));
		$str_split = explode('.', $str);
		if(count($str_split) > 1) {
			list($i, $d) = $str_split;
		} else {
			$i = $str;
			$d = 0;
		}
		return (double) ($i . '.' . substr((string) $d,0, $decimals));
	}
	
	private static function stringToArray(string $str, string $separator) {
		return explode($separator, $str);
	}
	
}