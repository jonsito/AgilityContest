<?php
/*
tools.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * Newer php 7.1 lacks of getAllHeaders() method.
 * so implement it
 */
if (!function_exists('getAllHeaders')) {
    function getAllHeaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

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

/* semaphores does not exist in windozes so create */
if (!function_exists('sem_get')) {
    function sem_get($key) {
        return fopen(__DIR__ ."/../../logs/semaphore_{$key}.sem", 'w+');
    }
    function sem_acquire($sem_id) {
        return flock($sem_id, LOCK_EX | LOCK_NB);
    }
    function sem_release($sem_id) {
        return flock($sem_id, LOCK_UN);
    }
	function sem_remove($sem_id) {
        $meta_data = stream_get_meta_data($sem_id);
        $filename = $meta_data["uri"];
        fclose($sem_id);
        unlink($filename);
	}
}

/* poor's man implementation of ftok for windozes, required for semaphores */
if( !function_exists('ftok') ) {
    function ftok ($filePath, $projectId) {
        $fileStats = stat($filePath);
        if (!$fileStats) {
            return -1;
        }

        return sprintf('%u',
            ($fileStats['ino'] & 0xffff) | (($fileStats['dev'] & 0xff) << 16) | ((ord($projectId) & 0xff) << 24)
        );
    }
}

/* generate a pseudo-random string of provided length (def:16) */
function getRandomString($length = 16) {
    if (function_exists('random_bytes')) { // real random, secure numbers but only available after php >7.0
        return substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes($length))), 0, $length);
    } else { // fallback when no PHP 7.0 available
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}

/* check for positive, negative or zero */
function sign($n) {
    return ($n>0) - ($n<0);
}

function enterCriticalRegion($key) {
	$sem=sem_get($key);
	// this
	sem_acquire($sem);
	return $sem;
}

function leaveCriticalRegion($sem) {
	sem_release($sem);
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

/**
 * Translate a number with arbitrary precission to fixed point decimal
 * @param {float} $number number to translate
 * @param {int} $prec number of decimals
 * @return {string} resulting number
 */
function number_format2($number,$prec) {
    // return round($number,$prec,PHP_ROUND_HALF_UP); // round
    // return round($number,$prec,PHP_ROUND_HALF_DOWN); // trunc. Fails due to half down not working as expected
    return bcmul(strval($number),1,$prec);
}

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
	return false;
}

// compile a range-comma (ie: "1,2,5-9" ) string to an array of values
function expand_range($range,$sep=",") {
    if (!is_string($range)) return array(); // empty
    $a=explode($sep,trim($range));
    $result=array();
    for ($n=0; $n<count ($a); $n++) {
        if (is_numeric($a[$n])) { $result[]= intval($a[$n]); continue;} // just add data
        if (preg_match('/^\d+-\d+$/',$a[$n])===FALSE) continue; // invalid syntax
        $r=explode("-",$a[$n]);
        $f=intval($r[0]);
        $t=intval($r[1]);
        if ($t<$f) continue; // invalid negative range specification
        for($i=$f;$i<=$t;$i++) $result[]=$i;
    }
    return $result;
}

// check if we are using HTTPS.
// notice this may fail on extrange servers when https is not by mean of port 443
function is_https(){
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') return true;
	return false;
}

/** convert an excel date format into unix epoch seconds */
function excelTimeToSeconds($exceldate) {
    return intval(floor(($exceldate - 25569) * 86400));
}

function normalize_license($license) {
    // remove every non alphanumeric chars
    $lic=preg_replace("/[^A-Za-z0-9 ]/", '', $license);
    // PENDING convert [0ABCD]xx to proper format (3 digits)
    return strtoupper($lic);
}

function normalize_filename($fname) {
    $fname=trim($fname);
    $fname=str_replace(" ","_",$fname);
    $fname=str_replace("/","",$fname);
    $fname=str_replace(".","",$fname);
    $fname=str_replace("+","",$fname);
    $fname=str_replace("_-_","_",$fname);
    return $fname;
}

function get_browser_name() {
    $user_agent=$_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    return 'Other';
}

/**
 * Parse provided string and escape special chars to avoid SQL injection problems
 * NOTICE: THIS IS ONLY VALID FOR MYSQL "native escape mode" on UTF-8 encoding
 * DO NOT FORCE "ANSI" escape mode
 * @param {string} $str
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
 * capitalize first letter of every word utf8 strings
 * notice: ucwords() doesn't work with utf8
 * @param $str
 */
function toUpperCaseWords($str) {return mb_convert_case($str, MB_CASE_TITLE, "UTF-8"); }

/**
 * Parse an string and return matching boolean value
 * @param {string} $var  text to be evaluated
 * @return bool|string true, false,or same text if cannot decide
 */
function toBoolean($var) {
	if (is_null($var)) return false;
	if (is_bool($var)) return $var;
	if (is_string($var)) $var=strtolower(trim($var));
	$t=array (1,true,"1","t","true","on","s","si","sí","y","yes","ja","oui","da");
	if ( in_array($var,$t,true) ) return true;
	return false;
}

/**
 * @param {string} $var data to be checked
 * @return {bool|null} true:yes false:no null: not  a valid answer
 */
function parseYesNo($var) {
    if (is_null($var)) return false;
    if (is_bool($var)) return $var;
    if (is_string($var)) $var=strtolower(trim($var));
    $t=array (1,true,"x","1","t","true","on","s","si","sí","y","yes","ja","oui","da");
    if ( in_array($var,$t,true) ) return true;
    $f=array (0,false,"","0","f","false","off","n","no","ez","non","nein","niet");
    if ( in_array($var,$f,true) ) return false;
    // arriving here means neither true nor false valid items, so return nothing
    return null;
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

// converts "YYYYmmdd_hhmm" to "YYYY-mm-dd hh:mm:00"
function toLongDateString($str) {
    // set "updated" to be the same date of version: yyyymmmdd_hhmm
    $year=substr($str,0,4);
    $month=substr($str,4,2);
    $day=substr($str,6,2);
    $hour=substr($str,9,2);
    $min=substr($str,11,2);
    return "{$year}-{$month}-{$day} {$hour}:{$min}:00";
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
	$gender=strtolower(trim(iconv('UTF-8','ASCII//TRANSLIT',$gender)));
	if ($gender==="") return '-';
	if (in_array($gender,$female)) return 'F';
	if (in_array($gender,$male)) return 'M';
	// perhaps should try to detect here if first letter is m/h/f
	return '-';
}

/**
 * Try to obtain dog grade according provided string
 * @param {string} $cat user provided category
 * @param {integer} $int federation (RSCE>=2023 changes XL and L names)
 * @return {string} X,L,M,S,T,- detected category
 */
function parseCategory($cat,$fed=0) {
    $cats= array (
	    'M' => array('m','medium','midi','mid','med','400','40','4'),
	    'S' => array('s','small','mini','min','300','30','3'),
	    'T' => array('t','enano','tiny','toy','xs','x-small','x-short','extra-short','250','200','25','20','2')
    );
    if ($fed!=0) {
        $cats['X'] = array('x','extra','xlarge','xl','x-large','extra-large','600','60','6');
        $cats['L'] = array('l','large','standard','estandar','std','i','intermediate','intermedia','inter','int','500','50','5');
    } else {
        $cats['X'] = array('x','extra','xlarge','xl','x-large','l','large','standard','estandar','std','extra-large','600','60','6');
        $cats['L'] = array('i','intermediate','intermedia','inter','int','500','50','5');
    }
	if (is_null($cat)) return '-';
    $str = preg_replace("/[^A-Za-z0-9]/u", '', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$cat)));
    $str = preg_replace('/\D+(\d+)/i','${1}',$str); // try to resolve "Clase XX" RFEC patterns
    foreach ( $cats as $key => $values) {
        if (in_array($str, $values)) return $key;
    }
	return '-';
}

/**
 * same as parse grade but look for exact match, not just strpos
 * this is used for search in database selecting by grade
 * @param {string} $str provided user string
 * @return string found grade or '' if not found
 */
function parseGrade($grad) {
    $grados = array (
        'Baja' => array("out","baj","baja"),
        'Ret'  => array("ret","retired","retirado"),
        'Jr'   => array("j","jr","jun","junior"),
        'Sr'   => array("s","sr","sen","senior"),
        'P.A.' => array("p","pa","pre","preagility","0","g0","grado0","grade0","a0","ini","inic","iniciacion"),
        'GI'   => array("g1","grado1","grade1","1","i","a1","ai","gi","gradoi","gradei","pro","promo","promocion"),
        'GII'  => array("g2","grado2","grade2","2","ii","a2","aii","gii","gradoii","gradeii","com","comp","competicion"),
        'GIII'  =>array("g3","grado3","grade3","3","iii","a3","aiii","giii","gradoiii","gradeiii")
    );
    if (is_null($grad)) return '-';
    $str=preg_replace("/[^A-Za-z0-9]/u", '', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$grad)));
    foreach ( $grados as $key => $values) {
        if (in_array($str,$values)) return $key;
    }
    return '-';
}

function parseHandlerCat($cat) {
    $cats =array(
    'I' => array('i','ch','child','children','infantil','infantiles'),
    'J' => array('j','jr','junior','juvenil','juveniles','young'),
    'A' => array('a','adult','adults','adulto','adultos','absolut','absoluta'),
    'S' => array('s','sr','senior','seniors','veterans','veterano','veteranos'),
    'R' => array('r','ret','retired','retirado','retirados','baja'),
    'P' => array('p','pa','par','para','paraagility','para-agility')
    );
    if (is_null($cat)) return '-';
    $str=preg_replace("/[^A-Za-z0-9]/u", '', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$cat)));
    foreach ( $cats as $key => $values) {
        if (in_array($str,$values)) return $key;
    }
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
function testAndSet(&$data,$name,$type,$def,$esc=true) {
	if (isset($_REQUEST[$name])) $data[$name]=http_request($name,$type,$def,$esc);
	return $data;
}

/**
 * Generate a random password of "n" characters
 * @param {number} $chars Number of characters. Default to 8
 * @return {string} requested password
 */
function random_password($chars = 8) {
   $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
   return substr(str_shuffle($letters), 0, $chars);
}

/**
 * Randomize array content
 * @param {array} $a array a reordenar
 * @return {array} shuffled array
 */
function aleatorio($a) { shuffle($a); return $a; }

/**
 * Generate a default client session name
 * generate string random(8)@client.ip.address
 * take care on ipv6 address by replace ':' with ';'
 * @return {string} default session name
 */
function getDefaultClientName($base) {
    $rnd = random_password(8);
    $addr = $_SERVER['REMOTE_ADDR'];
    return str_replace(":",".","{$base}_{$rnd}@{$addr}"); // filter ipv6 colon
}

/**
 * Remove recursively a directory
 * @param {string} $dir PATH TO remove
 * @return bool operation result
 */
function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

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
 * Insert item (if not already present) at the end of a comma-separated list
 * @param {int} $item
 * @param {string} $list
 * @return {string} new list
 */
function list_insert($item,$list='BEGIN,END') {
    $str = ",$item,";
    if (strpos($list,$str)!==false) return $list; // already present
    // componemos el tag que hay que insertar
    $myTag="$item,END";
    // y lo insertamos en lugar que corresponde ( al final )
    return str_replace ( "END", $myTag, $list );
}

/**
 * Remove item from comma-separated list
 * @param {int} $item
 * @param {string} $list
 * @return {string} new list
 */
function list_remove($item,$list='BEGIN,END') {
    $str = ",$item,";
    return str_replace ( $str, ",", $list );
}

/**
 * Inserta un perro en el espacio indicado, sacandolo del sitio inicial
 * @param {integer} $from sitio inicial (dog ID)
 * @param {integer} $to sitio final
 * @param {integer} $where insertart "encima" (0) o "debajo" (1)
 * @param {string} $list
 * @return {string} new list
 */
function list_move($from,$to,$where,$list='BEGIN,END') {
    if ($from==$to) return $list; // no need to change anything
	// extraemos "from" de donde este y lo guardamos
	$str = ",$from,";
	$list = str_replace ( $str , "," , $list );
	// insertamos 'from' encima o debajo de 'to' segun el flag 'where'
	$str1 = ",$to,";
	$str2 = ($where==0)? ",$from,$to," : ",$to,$from,";
	// retornamos el resultado
	return str_replace( $str1 , $str2 , $list );
}

/**
 * Tells if a item is included in list
 * @param {integer} $item
 * @param {string} $list
 * @return {bool} false or true ( found, notfound
 */
function list_isMember($item,$list="BEGIN,END") {
    $str=",$item,";
    return (strpos($list,$str)===FALSE)?false:true;
}

// Function to check response time to http connect request
// also used as tcp ping test
function isNetworkAlive(){
    return -1; // required cause www.agilitycontest.es is no longer available
    $starttime = microtime(true);
    $file      = @fsockopen ("185.129.248.76" /* www.agilitycontest.es */, 80, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    if (!$file) return -1;  // Site is down
    fclose($file);
    $status = ($stoptime - $starttime) * 1000;
    return floor($status);
}

/**
 * Try to get a file from url
 * Depending on config try several methods
 *
 * @param {string} $url filename or URL  to retrieve
 */
function retrieveFileFromURL($url) {
    $scheme=parse_url($url,PHP_URL_SCHEME);
    if ($scheme !== "file") {
        // before continue check internet conectivity
        if (isNetworkAlive()<0) return FALSE;
    }
    // if enabled, use standard file_get_contents
    if (ini_get('allow_url_fopen') == true) {
        $res=@file_get_contents($url); // omit warning on faillure
        // on fail, try to use old way to retrieve data
        if ($res!==FALSE) return $res;
    }
    // if not enable, try curl
    if (function_exists('curl_init')) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/../../config/cacert.pem");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // tell curl to allow redirects up to 5 jumps
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    // arriving here means error
    return FALSE;
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
		$sn=$s[$n];
        // as alphabetic sorting order does not work here we need a little sql trick
		if (strcmp($s[$n],'Categoria')==0) {
            $sn=" FIELD(Categoria,'X','L','M','S','T','-')";
        }
		$res = $res . $sn . " " . $o[$n];
	}
	return $res;
}

/**
 * Try to locate icon in the filesystem
 * Analyze provided icon name. If provides path, verify and use it
 * On invalid path or not provided,search into iconpath
 * @param $fedname ( federation 'Name' -not ID- )
 * @param $name (icon path or name )
 * @return $string full path name to load image from (server side)
 */
function getIconPath($fedname,$name) {
	static $iconPathTable = array();
	$fedname=strtolower($fedname);
	$name=basename($name); // to avoid sniffing extract name from path and force use own iconpaths
	$iconpath=array(
		__DIR__. "/../images/logos", // standard club icon location
		__DIR__. "/i18n",			// standard countri flags location
		__DIR__. "/../images/supporters", // where to store supporters logos
        __DIR__. "/../lib/jquery-easyui-1.4.2/themes/icons", // app logos
        __DIR__. "/modules/federaciones/$fedname" // federation logos
	);
	if (array_key_exists("$fedname - $name",$iconPathTable)) return $iconPathTable["$fedname - $name"];
	foreach ($iconpath as $path) {
		if (!file_exists("{$path}/{$name}")) continue;
		$iconPathTable["$fedname - $name"]="{$path}/{$name}";
		return "{$path}/{$name}";
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

function compatible_categories($height,$cat) {
    switch ($height) {
        case 3: // join XL and ST
            switch($cat) {
                case '-': return 'XLMST';
                case 'X': return 'XL';
                case 'L': return 'XL';
                case 'M': return 'M';
                case 'S': return 'ST';
                case 'T': return 'ST';
            }
            break;
        case 4: // join XL
            switch($cat) {
                case '-': return 'XLMST';
                case 'X': return 'XL';
                case 'L': return 'XL';
                case 'M': return 'M';
                case 'S': return 'S';
                case 'T': return 'T';
            }
            break;
        case 5: // do not join
            return ($cat==="-")?'XLMST':$cat;
    }
    // arriving here returns default
    return 'XLMST';
}

/**
 * Check if any of provided categories in $from are included in valid ones in $to
 * @param {string} $from categories to check
 * @param {integer} $number of heights
 * @param {string | integer} $to valid categories, or mode
 * return {boolean} true or false
 */
function category_match($from,$heights,$to="-XLMST") {
    if (is_numeric($to)) {
        switch (intval($to)+100*$heights) {
            // 3 heights add X to L and T to S
            case 300: $to='XL'; break;         // L
            case 301: $to='M'; break;         // M
            case 302: $to='ST'; break;         // S
            case 303: $to='MS'; break;        // MS
            case 304: $to='XLMST'; break;       // LMS
            case 305: $to='T'; break;         // T
            case 306: $to='XLM'; break;        // LM
            case 307: $to='ST'; break;        // ST
            case 308: $to='XLMST'; break;      // LMST
            case 309: $to='X'; break;         // X
            case 310: $to='XL'; break;       // XL
            case 311: $to='MST'; break;      // MST
            case 312: $to='XLMST'; break;    // XLMST
            // 4 heights: add X to L
            case 400: $to='XL'; break;         // L
            case 401: $to='M'; break;         // M
            case 402: $to='S'; break;         // S
            case 403: $to='MS'; break;        // MS
            case 404: $to='XLMS'; break;       // LMS
            case 405: $to='T'; break;         // T
            case 406: $to='XLM'; break;        // LM
            case 407: $to='ST'; break;        // ST
            case 408: $to='XLMST'; break;      // LMST
            case 409: $to='X'; break;         // X
            case 410: $to='XL'; break;       // XL
            case 411: $to='MST'; break;      // MST
            case 412: $to='XLMST'; break;    // XLMST
            // 5 heights: match to mode
            case 500: $to='L'; break;         // L
            case 501: $to='M'; break;         // M
            case 502: $to='S'; break;         // S
            case 503: $to='MS'; break;        // MS
            case 504: $to='LMS'; break;       // LMS
            case 505: $to='T'; break;         // T
            case 506: $to='LM'; break;        // LM
            case 507: $to='ST'; break;        // ST
            case 508: $to='LMST'; break;      // LMST
            case 509: $to='X'; break;         // X
            case 510: $to='XL'; break;       // XL
            case 511: $to='MST'; break;      // MST
            case 512: $to='XLMST'; break;    // XLMST

            default: $to='-XLMST'; break;   // -
        }
    }
	if (strpos($to,"-")!==false) return true; // "-" matches any
	$a_arr = str_split($from);
    $r_arr = str_split($to);
    $common = implode(array_unique(array_intersect($a_arr, $r_arr)));
	return ($common==="")?false:true;
}

function sqlFilterCategoryByMode($mode,$heights,$prefix=""){
    // select valid categories according mode and heights
    do_log("mode:{$mode} heights:{$heights} prefix:{$prefix}");
    switch($mode+100*$heights) {
        // 3 heights ( add 'X' to 'L' and 'T' to 'S'
        case 300: /* Large */     return "AND ( {$prefix}Categoria IN ('X','L') ) "; break;
        case 301: /* Medium */    return "AND ( {$prefix}Categoria='M' ) "; break;
        case 302: /* Small */     return "AND ( {$prefix}Categoria IN ('S','T') ) "; break;
        case 303: /* Med+Small */ return "AND ( {$prefix}Categoria IN ('M','S','T') ) "; break;
        case 304: /* L+M+S */     return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') )"; break;
        case 305: /* Toy */       return "AND ( {$prefix}Categoria='T' ) "; break;
        case 306: /* L+M */       return "AND ( {$prefix}Categoria IN ('X','L','M') ) "; break;
        case 307: /* S+T */       return "AND ( {$prefix}Categoria IN ('S','T') ) "; break;
        case 308: /* L+M+S+T */   return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') ) "; break;
        case 309: /* XtraLarge */ return "AND ( {$prefix}Categoria='X' ) "; break;
        case 310: /* XL + L */    return "AND ( {$prefix}Categoria IN ('X','L') ) "; break;
        case 311: /* M+S+T */     return "AND ( {$prefix}Categoria IN ('M','S','T') ) "; break;
        case 312: /* X+L+M+S+T */ return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') ) "; break;
        // 4 heights ( add X to L )
        case 400: /* Large */     return "AND ( {$prefix}Categoria IN ('X','L') ) "; break;
        case 401: /* Medium */    return "AND ( {$prefix}Categoria='M' ) "; break;
        case 402: /* Small */     return "AND ( {$prefix}Categoria='S' ) "; break;
        case 403: /* Med+Small */ return "AND ( {$prefix}Categoria IN ('M','S') ) "; break;
        case 404: /* L+M+S */     return "AND ( {$prefix}Categoria IN ('X','L','M','S') )"; break;
        case 405: /* Toy */       return "AND ( {$prefix}Categoria='T' ) "; break;
        case 406: /* L+M */       return "AND ( {$prefix}Categoria IN ('X','L','M') ) "; break;
        case 407: /* S+T */       return "AND ( {$prefix}Categoria IN ('S','T') ) "; break;
        case 408: /* L+M+S+T */   return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') ) "; break;
        case 409: /* XtraLarge */ return "AND ( {$prefix}Categoria='X' ) "; break;
        case 410: /* XL + L */    return "AND ( {$prefix}Categoria IN ('X','L') ) "; break;
        case 411: /* M+S+T */     return "AND ( {$prefix}Categoria IN ('M','S','T') ) "; break;
        case 412: /* X+L+M+S+T */ return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') ) "; break;
        // 5 heights ( leave same as mode states )
        case 500: /* Large */     return "AND ( {$prefix}Categoria='L' ) "; break;
        case 501: /* Medium */    return "AND ( {$prefix}Categoria='M' ) "; break;
        case 502: /* Small */     return "AND ( {$prefix}Categoria='S' ) "; break;
        case 503: /* Med+Small */ return "AND ( {$prefix}Categoria IN ('M','S') ) "; break;
        case 504: /* L+M+S */     return "AND ( {$prefix}Categoria IN ('L','M','S') )"; break;
        case 505: /* Toy */       return "AND ( {$prefix}Categoria='T' ) "; break;
        case 506: /* L+M */       return "AND ( {$prefix}Categoria IN ('L','M') ) "; break;
        case 507: /* S+T */       return "AND ( {$prefix}Categoria IN ('S','T') ) "; break;
        case 508: /* L+M+S+T */   return "AND ( {$prefix}Categoria IN ('L','M','S','T') ) "; break;
        case 509: /* XtraLarge */ return "AND ( {$prefix}Categoria='X' ) "; break;
        case 510: /* XL + L */    return "AND ( {$prefix}Categoria IN ('X','L') ) "; break;
        case 511: /* M+S+T */     return "AND ( {$prefix}Categoria IN ('M','S','T') ) "; break;
        case 512: /* X+L+M+S+T */ return "AND ( {$prefix}Categoria IN ('-','X','L','M','S','T') ) "; break;
        default: return "";
    }
}

// comodity functions from Mangas.php
function isMangaAgility($tipo) { return in_array($tipo,array(1,3,5,6,7,8,9,16,17,25,26,32,34,36,38)); }
function isMangaJumping($tipo) { return in_array($tipo,array(2,4,10,11,12,13,14,27,28,29,33,35,37,39)); }
function isMangaKO($tipo) { return in_array($tipo,array(15,18,19,20,21,22,23,24)); }
function isMangaGames($tipo) { return in_array($tipo,array(29,30)); }
function isMangaWAO($tipo) { return in_array($tipo,array(25,26,27,28,29,30,31)); }
function isMangaEquipos3($tipo) { return in_array($tipo,array(8,13)); }
function isMangaEquipos4($tipo) { return in_array($tipo,array(9,14)); }
function isMangaEquipos($tipo) { return in_array($tipo,array(8,9,13,14)); }
function isMangaPreAgility($tipo) { return in_array($tipo,array(1,2)); }
function isMangaJunior($tipo) { return in_array($tipo,array(32,33)); }
function isMangaSenior($tipo) { return in_array($tipo,array(34,35)); }
function isMangaInfantil($tipo) { return in_array($tipo,array(36,37)); }
function isMangaParaAgility($tipo) { return in_array($tipo,array(38,39)); }
function isMangaOpen($tipo) { return in_array($tipo,array(7,12)); }

function assertClosedJourney($jornada) {
    $msg=_("Current journey is closed. cannot modify");
    if (is_object($jornada) && ($jornada->Cerrada!=0)) throw new Exception($msg);
    if (is_array($jornada) && ($jornada['Cerrada']!=0)) throw new Exception($msg);
}

// pinta una bola de billar numerada con el color de fondo y de la bola especificados
// se usa en el manejo de pruebas WAO-Games
function createNumberedBall($color,$bgcolor,$number) {
    // crear una imagen "vacia"
    $imagen = imagecreate(51, 51);
    // color de fondo
    $c=hex2rgb($bgcolor);
    imagecolorallocate($imagen, $c[0], $c[1], $c[2]); // primer colorallocate sets background
    //color para la bola
    $c=hex2rgb($color);
    $bola = imagecolorallocate($imagen, $c[0], $c[1], $c[2]);
    // colores blanco y negro
    $black=imagecolorallocate($imagen,0,0,0);
    $white=imagecolorallocate($imagen, 255,255, 255);
    // pintamos bola coloreada
    imagefilledellipse($imagen, 25, 25, 49, 49, $bola);
    // pintamos centro de la bola y el texto
    imagefilledellipse($imagen, 25, 25, 30, 30, $white);
    // putenv('GDFONTPATH=' . realpath('.'));
    $font = __DIR__."/arial.ttf";
    imagettftext($imagen, 20, 0, (strlen($number)==1)?17:11, 35, $black, $font, $number);
    return $imagen;
}


/**
 * check if running in master server
 *
 * notice that this may fail on server with multiple interfaces
 * @param {object} $config Config object
 * @param {object} $logger Logger object
 */
function inMasterServer($config,$logger=null) {
    // first of all, check internet conectivity
    if (isNetworkAlive()<0) return false;
    // compare IP's as reverse lookup may fail in some servers
    $ip=gethostbyname($config->getEnv('master_server'));
    if ($logger){
        $logger->trace("master_server: {$ip} server_addr {$_SERVER['SERVER_ADDR']} ");
	}
    return ($_SERVER['SERVER_ADDR']===$ip)?true:false;
}

// Stream handler to read from global variables as from file or URL
// from http://www.fpdf.org/en/script/script45.php
class VariableStream {
    private $varname;
    private $position;

    // path: "var://imagename"
    function stream_open($path, $mode, $options, &$opened_path) {
        $url = parse_url($path);
        $this->varname = $url['host'];
        if(!isset($GLOBALS[$this->varname])) {
            trigger_error('Global variable '.$this->varname.' does not exist', E_USER_WARNING);
            return false;
        }
        $this->position = 0;
        return true;
    }

    function stream_read($count) {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    function stream_eof() {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    function stream_tell() {
        return $this->position;
    }

    function stream_seek($offset, $whence) {
        if($whence==SEEK_SET) {
            $this->position = $offset;
            return true;
        }
        return false;
    }

    function stream_stat() {
        return array();
    }
}

/**
 * Clase para enumerar los interfaces de red del servidor
 */
class networkInterfaces {
	var $osName;
	var $interfaces;

	function __construct() {
		$this->osName = strtoupper(PHP_OS);
	}

    /**
     * Ping to requested host with provided ( or defaulted ) parameters
     * @return int Latency, in ms.
     */
	function ping_address($host,$ttl=64,$timeout=1) {
        $latency = false;
	    // this is to protect data injection in "exec" command
        $ttl = escapeshellcmd($ttl);
        $timeout = escapeshellcmd($timeout);
        $host = escapeshellcmd($host);
        // prepare ping command depending on OS
        switch ($this->osName) {
            case 'WINDOWS':
            case 'WIN32':
            case 'WINNT':
                // -n = number of pings; -i = ttl; -w = timeout (in milliseconds).
                $exec_string = 'ping -n 1 -i ' . $ttl . ' -w ' . ($timeout * 1000) . ' ' . $host;
                break;
            case 'LINUX':
                // -n = numeric output; -c = number of pings; -t = ttl; -W = timeout
                $exec_string = 'ping -n -c 1 -t ' . $ttl . ' -W ' . $timeout . ' ' . $host . ' 2>&1';
                break;
            case 'DARWIN':
                // -n = numeric output; -c = number of pings; -m = ttl; -t = timeout.
                $exec_string = 'ping -n -c 1 -m ' . $ttl . ' -t ' . $timeout . ' ' . $host;
                break;
            default     : break;
        }
        exec($exec_string, $output, $return);
        // Strip empty lines and reorder the indexes from 0 (to make results more
        // uniform across OS versions).
        $this->commandOutput = implode($output, '');
        $output = array_values(array_filter($output));
        // If the result line in the output is not empty, parse it.
        if (!empty($output[1])) {
            // Search for a 'time' value in the result line.
            $response = preg_match("/time(?:=|<)(?<time>[\.0-9]+)(?:|\s)ms/", $output[1], $matches);
            // If there's a result and it's greater than 0, return the latency.
            if ($response > 0 && isset($matches['time'])) {
                $latency = round($matches['time']);
            }
        }
        return $latency;
    }

	function get_interfaces($removelocal=true) {
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
				// does not work: its i18 dependend, has some upper/lowercase issues and requires "/all" parameter
                // $macPattern = '/'._('Physical').'[^:]+: ([a-fA-F0-9]{2}-){5}[a-fA-F0-9]{2}/';
				break;
			case 'LINUX': $ipRes = shell_exec('/sbin/ifconfig');
				$ipPattern = '/inet ([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/';
				// $macPattern = '/ether ([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/';
				break;
            case 'DARWIN': $ipRes = shell_exec('ifconfig'); // TODO: check correctness
                $ipPattern = '/inet ([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})/';
                // $macPattern = '/ether ([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/';
                break;
			default     : break;
		}
		if (preg_match_all($ipPattern, $ipRes,$matches)) {
			$this->interfaces = $matches[1];
			if ($removelocal) return array_diff($this->interfaces,array('127.0.0.1'));
			return $this->interfaces;
		}
        return array();
	}
}
?>
