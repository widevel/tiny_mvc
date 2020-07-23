<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/
function tmpl_arguments_forward(array $vars) {
	unset($vars['__var_value'], $vars['__var_name']);
	return $vars;
}