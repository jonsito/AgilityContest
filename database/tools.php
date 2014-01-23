<?php
/**
 * Several utility functions 
 */

/* boolval is only supported in PHP > 5.3 */
if( ! function_exists('boolval')) {
	function boolval($var)	{
		return !! $var;
	}
}

/* disable send compressed data to client from apache */
function disable_gzip() {
	@ini_set('zlib.output_compression', 'Off');
	@ini_set('output_buffering', 'Off');
	@ini_set('output_handler', '');
	@apache_setenv('no-gzip', 1);
}
// disable_gzip();

/**
 * get a variable from _REQUEST array
 * @param {string} $name variable name
 * @param {string} $type default type (i,s,b)
 * @param {string} $default default value. may be null
 * @return requested value (int,string,bool) or null if invalid type
 */
function http_request($name,$type,$default) {
	switch ($type) {
		case "i": return isset($_REQUEST[$name])?intval($_REQUEST[$name]):$default;
		case "s": return isset($_REQUEST[$name])?strval($_REQUEST[$name]):$default;
		case "b": return isset($_REQUEST[$name])?boolval($_REQUEST[$name]):$default;
	}
	do_log("request() invalid type:$type requested"); 
	return null; 
}
?>