<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

function get_microtime(float $microtime = null) :int {
	$microtime_split = explode('.', ($microtime === null ? microtime(true) : $microtime));
	return count($microtime_split) > 1 ? (int) $microtime_split[1] : 0;
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

function timeElapsed(int $time) {

    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
	$plural = ['mes' => 'es'];
    $tokens = array (
        31536000 => 'año',
        2592000 => 'mes',
        604800 => 'semana',
        86400 => 'dia',
        3600 => 'hora',
        60 => 'minuto',
        1 => 'segundo'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?(array_key_exists($text, $plural) ? $plural[$text] : 's'):'');
    }

}