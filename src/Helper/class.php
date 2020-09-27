<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function get_class_const($class, string $name) {
	$ref = new ReflectionClass((is_object($class) ? get_class($class) : $class));
	return $ref->getConstant($name);
}