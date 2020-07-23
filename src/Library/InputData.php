<?php

namespace TinyMvc\Library;
use TinyMvc\InputData\Element;

class InputData {
	
	public static function getElement(array $options) {
		$element = new Element;
		if(array_key_exists('name', $options) && is_string($options['name'])) $element->setName($options['name']);
		if(array_key_exists('cast', $options) && is_string($options['cast'])) $element->setCast($options['cast']);
		if(array_key_exists('max_length', $options) && is_integer($options['max_length'])) $element->setMaxLength($options['max_length']);
		if(array_key_exists('array_max_elements', $options) && is_integer($options['array_max_elements'])) $element->setArrayMaxElements($options['array_max_elements']);
		if(array_key_exists('map_func', $options) && is_string($options['map_func'])) $element->setMapFunc($options['map_func']);
		if(array_key_exists('preg_replace', $options) && is_string($options['preg_replace'])) $element->setPregReplace($options['preg_replace']);
		if(array_key_exists('default_value', $options)) $element->setDefaultValue($options['default_value']);
		if(array_key_exists('double_decimals', $options) && is_integer($options['double_decimals'])) $element->setDoubleDecimals($options['double_decimals']);
		if(array_key_exists('array_separator', $options) && is_string($options['array_separator'])) $element->setArraySeparator($options['array_separator']);
		return $element;
	}
}