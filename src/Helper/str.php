<?php
/**
 * PHP version 7.X
 * PACKAGE: TinyMvc
 * VERSION: 0.1
 * LICENSE: GNU AGPLv3
 *
 * @author     Marco iosif Constantinescu <marco.isfc@gmail.com>
*/

define('RANDOM_STRING_CHARSET', '1sABWhiUmaqOfnD6gJN2xTFYK7X9GeMQ0bCduPwc8pHRtv3loZzSrVjE4LIky5');
define('RANDOM_NUMBER_CHARSET', '0123456789');
define('UNSERIALIZE_ERR', -55557411145);

function generate_random_hash(string $algo = 'md5') :string {
	return hash($algo, (microtime() . generate_random_str(512) . random_bytes(16)));
}

function generate_random_str(int $length) :string {
	return substr(string_walk_recursive(RANDOM_STRING_CHARSET, 'str_shuffle', rand(5,15)),0, $length);
}

function generate_random_number(int $length) :string {
	return (string) substr(string_walk_recursive(RANDOM_NUMBER_CHARSET, 'str_shuffle', rand(5,15)),0, $length);
}

function string_walk_recursive(string $str, string $func, int $amount, int $index = 1) {
	$walk_func = call_user_func($func, $str);
	return $index >= $amount ? $walk_func : string_walk_recursive($walk_func, $func, $amount, $index + 1);
}

function aes_enc($data, $iv, $key, $b64 = true, $method = 'aes-256-cbc') {
	$encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
	return $b64 === true ? base64_encode($encrypted) : $encrypted;
}

function aes_dec($data, $iv, $key, $b64 = true, $method = 'aes-256-cbc') {
	return openssl_decrypt(($b64 === true ? base64_decode($data) : $data), $method, $key, 0, $iv);
}

function bytesToHuman(int $bytes, int $d)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, $d) . ' ' . $units[$i];
}

function fillStrRight(string $str, int $min_length, string $fill) {
	return $str . str_repeat($fill, posOrZero($min_length - strlen($str)));
}

function fillStrLeft(string $str, int $min_length, string $fill) {
	return str_repeat($fill, posOrZero($min_length - strlen($str))) . $str;
}

function posOrZero(int $n) {
	return $n < 0 ? 0 : $n;
}

function prevent_xss($str) {
	return strip_tags(htmlentities($str, ENT_QUOTES));
}

function str_format(string $str, ...$args) : string {
	return str_replace(array_map('str_format_args_walk', array_keys($args)), $args, $str);
}

function str_format_args_walk($n) :string {
	return '{' . $n . '}';
}

function float_fixed(float $f, int $d) {
    $split = explode('.', (string) $f);
    if(count($split) <= 1) return $f;
    list($i, $dec) = $split;
    return (float) ($i . '.' . substr($dec,0,$d));
}

function _var_dump($a) {
	ob_start();
	var_dump($a);
	return ob_get_clean();
}

function str_startwith($str, $prefix, $s = 0) {
	$str = strtolower($str);
	$prefix = strtolower($prefix);
	return substr($str,$s,strlen($prefix)) == $prefix;
}

function str_startwith_arr($str, $arr) {
	$r = false;
	foreach($arr as $v) {
		if(str_startwith($str, $v)) return true;
	}
	return $r;
}

function stripos_arr($str, $arr) {
	foreach($arr as $v) {
		if(stripos($str, $v) !== false) return true;
	}
	return false;
}

function _unserialize(string $serialized) {
	log_d("_unserialize: " . $serialized, "TinyMvc.Helper.Cmd");
	$data = unserialize($serialized);
	if ($serialized === 'b:0;' || $data !== false) return $data;
	return UNSERIALIZE_ERR;
}