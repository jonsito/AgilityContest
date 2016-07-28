<?php
/*
tools.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

/* add a new line in echo sentence */
function echon($str) { echo $str . "\n"; }

/* disable send compressed data to client from apache */
function disable_gzip() {
	@ini_set('zlib.output_compression', 'Off');
	@ini_set('output_buffering', 'Off');
	@ini_set('output_handler', '');
	@apache_setenv('no-gzip', 1);
}
// disable_gzip();

// convert a #rrggbb string to an array($r,$g,$b)
function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}

function is_color($str) {
	if (preg_match('/^#[a-f0-9]{6}$/i', $str)) return true;
	if (preg_match('/^#[a-f0-9]{3}$/i', $str)) return true;
}

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
	if (is_bool($var)) return $var;
	if (is_string($var)) $var=strtolower(trim($var));
	$t=array (1,true,"1","t","true","on","s","si","y","yes","ja","oui");
	if ( in_array($var,$t) ) return true;
	return false;
}

/**
 * convierte un string UTF-8 a la cadena ASCII mas parecida
 * @param $string
 */
function toASCII($string) {
	if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
		$string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
	}
	return $string;
}

/**
 * Try to obtain dog gender with provided data
 * @param {string} $gender user provided gender
 * @return 'M':male 'F':female '':cannot decide
 */
function parseGender($gender) {
	static $female = array('h','f','hembra','perra','female','bitch','chienne','gossa','cadela','cagna','hundin');
	static $male = array('m','macho','male','dog','chien','gos','cao','cane','hund');
	if (is_null($gender)) return '-';
	$gender=strtolower(trim(utf8_decode($gender)));
	if ($gender==="") return '-';
	if (in_array($gender,$female)) return 'H';
	if (in_array($gender,$male)) return 'M';
	// perhaps should try to detect here if first letter is m/h/f
	return '-';
}

/**
 * Try to obtain dog grade according provided string
 * @param {string} $cat user provided category
 * @return L,M,S,T,- detected category
 */
function parseCategory($cat) {
	static $l = array('l','large','standard','std','60','6');
	static $m = array('m','medium','midi','mid','med','50','5');
	static $s = array('s','small','mini','min','40','4');
	static $t = array('t','enano','tiny','toy','30','3','20','2'); // include junior as toy
	if (is_null($cat)) return '-';
	$cat=strtolower(trim(utf8_decode($cat)));
	if ($cat==="") return '-';
	if (in_array($cat,$l)) return 'L';
	if (in_array($cat,$m)) return 'M';
	if (in_array($cat,$s)) return 'S';
	if (in_array($cat,$t)) return 'T';
	// perhaps should try to detect here if first letter is m/h/f
	return '-';
}

/**
 * Try to deduce grade based on provided string
 * @param {string} $grad provided user string
 * @return string found grade or '-' if cannot decide
 */
function parseGrade($grad) {
	if (is_null($grad)) return '-';
	$grad=strtolower(trim(utf8_decode($grad)));
	if ($grad==="") return '-';
	if (strpos($grad,'pre')!==false) return 'P.A.';
	if (strpos($grad,'pa')!==false) return 'P.A.';
	if (strpos($grad,'p.a')!==false) return 'P.A.';
	if (strpos($grad,'0')!==false) return 'P.A.';
	if (strpos($grad,'iii')!==false) return 'GIII';
	if (strpos($grad,'ii')!==false) return 'GII';
	if (strpos($grad,'i')!==false) return 'GI'; // cuidado con el orden de estos tres ultimos
	if (strpos($grad,'3')!==false) return 'GIII';
	if (strpos($grad,'2')!==false) return 'GII';
	if (strpos($grad,'1')!==false) return 'GI';
	return '-';
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
		case "s": if ($a===_('-- Search --') ) $a=""; // filter "search" in searchbox  ( should already be done in js side)
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
 * @return {string} requested string or empty if not found
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

function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
	$length = strlen($needle);
	if ($length == 0) {
		return true;
	}
	return (substr($haystack, -$length) === $needle);
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
 * @return full path name to load image from (server side)
 */
function getIconPath($fedname,$name) {
	static $iconPathTable = array();
	$fedname=strtolower($fedname);
	$name=basename($name); // to avoid sniffing extract name from path and force use own iconpaths
	$iconpath=array(
		__DIR__."/../images/logos", // standard club icon location
		__DIR__."/i18n",			// standard countri flags location
		__DIR__."/../images/supporters", // where to store supporters logos
		__DIR__."/../modules/$fedname", // federation logos
	);
	if (array_key_exists("$fedname - $name",$iconPathTable)) return $iconPathTable["$fedname - $name"];
	foreach ($iconpath as $path) {
		if (!file_exists("$path/$name")) continue;
		$iconPathTable["$fedname - $name"]="$path/$name";
		return "$path/$name";
	}
	// arriving here means not found. Use enterprise logo :-)
	return __DIR__."/../images/logos/agilitycontest.png";
}

/**
 * Create a temporary file and return their name
 * @param {string} $path Directory where file should be created
 * @param {string} $prefix File base name
 * @param {string} $suffix File extension
 * @return string fill path name
 */
function tempnam_sfx($path, $prefix="tmp_",$suffix="") {
	do	{
		$file = $path."/".$prefix.mt_rand().$suffix;
		if ($suffix!=="") $file=$file.".".$suffix;
		$fp = @fopen($file, 'x');
	}
	while(!$fp);
	fclose($fp);
	return $file;
}

/**
 * Check if any of provided categories in $from are included in valid ones in $to
 * @param {string} $from categories to check
 * @param {string} $to valid categories
 * return {boolean} true or false
 */
function category_match($from,$to="-LMST") {
	if (strpos($to,"-")!==false) return true; // "-" matches any
	$a_arr = str_split($from);
    $r_arr = str_split($to);
    $common = implode(array_unique(array_intersect($a_arr, $r_arr)));
	return ($common==="")?false:true;
}

function mode_match($cat,$mode) {
	switch ($mode) {
		case 0: return category_match($cat,"L");
		case 1: return category_match($cat,"M");
		case 2: return category_match($cat,"S");
		case 3: return category_match($cat,"MS");
		case 4: return category_match($cat,"LMS");
		case 5: return category_match($cat,"T");
		case 6: return category_match($cat,"LM");
		case 7: return category_match($cat,"ST");
		case 8: return category_match($cat,"LMST");
	}
	return false; // invalid mode
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