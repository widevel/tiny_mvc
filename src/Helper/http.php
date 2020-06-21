<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function check_http_header($header, $value) {
	return array_key_exists($header, getallheaders()) && getallheaders()[$header] == $value;
}