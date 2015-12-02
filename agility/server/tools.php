<?php
/*
tools.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/**
 * Several utility functions 
 */

/* boolval is only supported in PHP > 5.3 */
if( ! function_exists('boolval')) {
	function boolval($var)	{
		return !! $var;
	}
}

/* echo a gettext'd value */
if( ! function_exists('_e')) {
	function _e($var)	{
		echo _($var);
	}
}

/* convert to utf-8 a gettext'd value */
if( ! function_exists('_utf')) {
	function _utf($var)	{
		return html_entity_decode(_($var));
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

// check if we are using HTTPS.
// notice this may fail on extrange servers when https is not by mean of port 443
function is_https(){
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') return true;
	return false;
}

/**
 * Parse provided string and escape special chars to avoid SQL injection problems
 * NOTICE: THIS IS ONLY VALID FOR MYSQL "native escape mode" on UTF-8 encoding
 * DO NOT FORCE "ANSI" escape mode
 * @param {string} $str
 */
function escapeString($str) {
    // return mysqli_real_escape_string($str); // only works with msqli
	$len=strlen($str);
	$res="";
	for($i=0;$i<$len;$i++) {
		switch($str[$i]) {
			case '\n': $a="\\"."n"; break;
			case '\r': $a="\\"."r"; break;
			case '"': $a="\\".'"'; break;
			case '\'': $a="\\"."'"; break;
			case '\b': $a="\\"."b"; break;
			case '\\': $a="\\"."\\"; break;
			case '%': $a="\\".'%'; break;
			// case '_': $a="\\"."_"; break;
			default: $a=$str[$i]; break;
		}
		$res .= $a;
	}
	return $res;
}

function toBoolean($var) {
	if (is_null($var)) return false;
	if (is_string($var)) $var=strtolower($var);
	$t=array (1,true,"1","t","true","on","s","si","y","yes","ja","oui");
	foreach($t as $item) { if ($var===$item) return true; }
	return false;
}

/**
 * get a variable from _REQUEST array
 * @param {string} $name variable name
 * @param {string} $type default type (i,s,b)
 * @param {string} $def default value. may be null
 * @param {boolean} $esc true if variable should be MySQL escape'd to avoid SQL injection
 * @return {object} requested value (int,string,bool) or null if invalid type
 */
function http_request($name,$type,$def,$esc=true) {
	$a=$def;
	if (isset($_REQUEST[$name])) $a=$_REQUEST[$name];
	if ($a===null) return null;
	switch ($type) {
		case "s": if ($a==="---- Buscar ----") $a="";
			if ($esc) return escapeString(strval($a));
			return strval($a);
		case "i": return intval($a);
		case "b":
			if ($a==="") return $def;
			return toBoolean($a);
		case "d": 
		case "f": return floatval(str_replace("," ,"." ,$a));
	}
	do_log("request() invalid type:$type requested"); 
	return null; 
}

/**
 * If requested name is present in http request retrieve it and add to provided array
 * 
 * @param {array} $data
 * @param {string} $name variable name
 * @param {string} $type default type (i,s,b)
 * @param {string} $def default value. may be null
 * @param {boolean} $esc true if variable should be MySQL escape'd to avoid SQL injection
 * @return array with inserted data
 */
function testAndSet($data,$name,$type,$def,$esc=true) {
	if (isset($_REQUEST[$name])) $data[$name]=http_request($name,$type,$def,$esc);
	return $data;
}

/**
 * Generate a random password of "n" characters
 * @param {number} $chars Number of characters. Default to 8
 * @return {string} requested password
 */
function random_password($chars = 8) {
   $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
   return substr(str_shuffle($letters), 0, $chars);
}

/**
 * Randomize array content
 * @param {array} $a array a reordenar
 * @return {array} shuffled array
 */
function aleatorio($a) { shuffle($a); return $a; }

/**
 * Return the substring starting after '$from' and ending before '$to'
 * @param {string} $str string to search into
 * @param {string} $from start tag
 * @param {string} $to end tag
 */
function getInnerString($str,$from="",$to="") {
		$str = " ".$str;
		$ini = strpos($str,$from);
		if ($ini == 0) return "";
		$ini += strlen($from);
		$len = strpos($str,$to,$ini) - $ini;
		if ($len<=0) return "";
		return substr($str,$ini,$len);
}

/**
 * Compose a valid ORDER sentence by mean of received comma-separaqted strings
 * from easyui sort & order http requests
 */
function getOrderString($sort,$order,$def) {
	if ($sort==="") return $def; // empty search string; 
	$s=explode(",",$sort);
	$o=explode(",",$order);
	$res="";
	for($n=0;$n<count($s);$n++) {
		if ($n!=0) $res.=",";
		$res = $res . $s[$n] . " " . $o[$n];
	}
	return $res;
}

/**
 * Try to locate icon in the filesystem
 * Analyze provided icon name. If provides path, verify and use it
 * On invalid path or not provided,search into iconpath
 * @param $fedname ( federation 'Name' -not ID- )
 * @param $name (icon path or name )
 */
function getIconPath($fedname,$name) {
	static $icontable = array();
	$fedname=strtolower($fedname);
	$name=basename($name); // to avoid sniffing extract name from path and force use own iconpaths
	$iconpath=array(
		__DIR__."/../images/logos", // standard club icon location
		__DIR__."/i18n",			// standard countri flags location
		__DIR__."/../images/supporters", // where to store supporters logos
		__DIR__."/../modules/$fedname", // federation logos
	);
	if (array_key_exists("$fedname - $name",$icontable)) return $icontable["$fedname - $name"];
	foreach ($iconpath as $path) {
		if (!file_exists("$path/$name")) continue;
		$icontable["$fedname - $name"]="$path/$name";
		return "$path/$name";
	}
	// arriving here means not found. Use enterprise logo :-)
	return __DIR__."/../images/logos/agilitycontest.png";
}

/**
 * Clase para enumerar los interfaces de red del servidor
 */
class networkInterfaces {
	var $osName;
	var $interfaces;

	function networkInterfaces() {
		$this->osName = strtoupper(PHP_OS);
	}

	function get_interfaces() {
		if ($this->interfaces){
			return $this->interfaces;
		}
        $ipPattern="";
        $ipRes="";
		switch ($this->osName) {
            case 'WINDOWS':
            case 'WIN32':
			case 'WINNT': $ipRes = shell_exec('ipconfig');
				$ipPattern = '/IPv4[^:]+: ([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/';
				break;
			case 'LINUX': $ipRes = shell_exec('ifconfig');
				$ipPattern = '/inet ([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/';
				break;
            case 'DARWIN': $ipRes = shell_exec('ifconfig'); // TODO: check correctness
                $ipPattern = '/inet ([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/';
                break;
			default     : break;
		}
		if (preg_match_all($ipPattern, $ipRes,$matches)) {
			$this->interfaces = $matches[1];
			return $this->interfaces;
		}
        return array();
	}
}
?>