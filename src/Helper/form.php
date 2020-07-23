<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function form_value(string $form_name, string $field, $default_value = null) {
	return service('form')->getFieldValue($form_name, $field, $default_value);
}