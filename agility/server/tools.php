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

/* disable send compressed data to client from apache */
function disable_gzip() {
	@ini_set('zlib.output_compression', 'Off');
	@ini_set('output_buffering', 'Off');
	@ini_set('output_handler', '');
	@apache_setenv('no-gzip', 1);
}
// disable_gzip();


/**
 * Parse provided string and escape special chars to avoid SQL injection problems
 * NOTICE: THIS IS ONLY VALID FOR MYSQL "native escape mode" on UTF-8 encoding
 * DO NOT FORCE "ANSI" escape mode
 * @param unknown $str
 */
function escapeString($str) {
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

/**
 * get a variable from _REQUEST array
 * @param {string} $name variable name
 * @param {string} $type default type (i,s,b)
 * @param {string} $def default value. may be null
 * @param {boolean} $esc true if variable should be MySQL escape'd to avoid SQL injection
 * @return requested value (int,string,bool) or null if invalid type
 */
function http_request($name,$type,$def,$esc=true) {
	$t=array (1,true,"1","on","true","si","yes","ja","oui");
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
			foreach($t as $item) { if ($a===$item) return true; }
			return false;
		case "d": 
		case "f": return doubleval($a);
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
 * @param number $chars Number of characters. Default to 8
 * @return requested password
 */
function random_password($chars = 8) {
   $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
   return substr(str_shuffle($letters), 0, $chars);
}

/**
 * Randomize array content
 * @param {array} $a array a reordenar
 * @return shuffled array
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
 * Obtiene el "modo" de presentación en funcion de rsce/rfec tipoRecorrido y categoria
 * @param {integer} $rsce 0:RSCE 1:RFEC
 * @param {integer} $recorrido 0:L/M/S/T separado 1:L/MS LM/ST mixto 2:LMS LMST conjunto
 * @param {integer} $categoria 0:L 1:M 2:S 3:T
 */
function getMangaMode($rsce,$recorrido,$categoria) {
	if ($rsce==0) { // RSCE
		switch($recorrido) {
			case 0: // recorrido separado
				if ($categoria==0) return 0;
				if ($categoria==1) return 1;
				if ($categoria==2) return 2;
				break;
			case 1: // large / small+medium
				if ($categoria==0) return 0;
				if ($categoria==1) return 3;
				if ($categoria==2) return 3;
				break;
			case 2: // recorrido conjunto
				if ($categoria==0) return 4;
				if ($categoria==1) return 4;
				if ($categoria==2) return 4;
				break;
		}
	} else { // RFEC
		switch(recorrido) {
			case 0: // recorrido separado
				if ($categoria==0) return 0;
				if ($categoria==1) return 1;
				if ($categoria==2) return 2;
				if ($categoria==3) return 5;
				break;
			case 1: // large+medium / small+tiny
				if ($categoria==0) return 6;
				if ($categoria==1) return 6;
				if ($categoria==2) return 7;
				if ($categoria==3) return 7;
				break;
			case 2: // recorrido conjunto
				if ($categoria==0) return 8;
				if ($categoria==1) return 8;
				if ($categoria==2) return 8;
				if ($categoria==3) return 8;
				break;
		}
	}
	return -1; // combinacion invalida
}

?>