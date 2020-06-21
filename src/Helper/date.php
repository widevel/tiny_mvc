<?php
/**
 * PHP version 7.X
 *
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
 */

function get_microtime(float $microtime = null) :int {
	return (int) explode('.', ($microtime === null ? microtime(true) : $microtime))[1];
}

function get_time_from_microtime_func(float $microtime) :int {
	return (int) explode('.', ($microtime === null ? microtime(true) : $microtime))[0];
}

function getMysqlDateTime(int $time = null) {
	return date('Y-m-d H:i:s', ($time === null ? time() : $time));
}

function getMysqlDate(int $time = null) {
	return date('Y-m-d', ($time === null ? time() : $time));
}