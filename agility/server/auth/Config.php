<?php 
/*
 Config.php

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

/** default values **/
define('AC_CONFIG_FILE',__DIR__."/config.ini"); // user definable configuration
define('AC_SYSTEM_FILE',__DIR__."/system.ini"); // system configuration.
define('AC_BATCH_FILE',__DIR__."/../../../settings.bat"); // to store lang info in windoze

/** Internacionalizacion. Idiomas **/
define('AC_LANG','es_ES');
define('AC_ACCEPT_LANG','0');

/** logging **/
define('AC_DEBUG_LEVEL',0);
define('AC_REGISTER_EVENTS',"0");
define('AC_RESET_EVENTS',"1");
define('AC_EVENT_PRINTER','');
define('AC_WIDE_PRINTER','0'); // defaults to 58mm wide POS printer

/** variables de la aplicacion principal **/
define('AC_PROXIMITY_ALERT',5);

/** entorno grafico **/
define('AC_EASYUI_THEME','default');
define('AC_EASYUI_BGCOLOR','#0000ff');
define('AC_EASYUI_HDRCOLOR','#00ff00');
define('AC_EASYUI_OPCOLOR','#c0c0c0');
define('AC_EASYUI_ROWCOLOR1','#ffffff');
define('AC_EASYUI_ROWCOLOR2','#c0c0c0');
define('AC_EASYUI_ROWCOLOR3','#c0c0f0');
define('AC_EASYUI_ROWCOLOR4','#c0f0f0');

/** personalizacion del videowall **/
define('AC_VW_POLLTIME',5);
define('AC_VW_HDRFG1','#000000');
define('AC_VW_HDRBG1','#FF7F00');
define('AC_VW_HDRFG2','#0000ff');
define('AC_VW_HDRBG2','#808080');
define('AC_VW_HDRFG3','#000000');
define('AC_VW_HDRBG3','#808080');
define('AC_VW_ROWCOLOR1','#ffffff');
define('AC_VW_ROWCOLOR2','#e0ebff');
define('AC_VW_ROWCOLOR3','#ffffcf');
define('AC_VW_ROWCOLOR4','#e0ebcf');
define('AC_VW_ALPHA',0.5);
define('AC_VW_CRHOMAKEY','#00ff00');
define('AC_VW_DATAPOSITION',1); // 0:hidden 1:top/right 2:down/rignt 3:down/center

/** generacion de PDF's **/
define('AC_PDF_TOPMARGIN', 10.0); // margen superior etiquetas
define('AC_PDF_LEFTMARGIN', 10.0); // margen izquierdo etiquetas
define('AC_PDF_LABELHEIGHT', 17.0); // Altura de las pegatinas mmts
define('AC_PDF_JOURNEYS', "1"); // incluir jornadas en catalogo
define('AC_PDF_HDRFG1','#000000');
define('AC_PDF_HDRBG1','#00FF00');
define('AC_PDF_HDRFG2','#0000ff');
define('AC_PDF_HDRBG2','#808080');
define('AC_PDF_ROWCOLOR1','#ffffff');
define('AC_PDF_ROWCOLOR2','#e0ebff');
define('AC_PDF_LINECOLOR','#808080');
define('AC_PDF_FONTFAMILY','Helvetica');

/** personalizacion del tablet **/
define('AC_TABLET_BEEP',"0");		// habilitar señal sonora al pulsar tecla (1) o deshabilita (0)
define('AC_TABLET_DND',"0");		// habilita cambiar orden de salida desde tablet (1) o deshabilita(0)
define('AC_TABLET_CHRONO',"0");		// habilita mostrar crono en tablet (1) o no (0)
define('AC_TABLET_NEXT',"0");		// acept vuelve a menu (0) o pasa al siguiente (1)
define('AC_TABLET_COUNTDOWN',"1");	// accion tras cuenta de 15 segundos 1:nada 2:crono 3:eliminado
define('AC_TABLET_KEYBOARD',"1");	// habilita el uso de teclas en en el tablet si portatil/notebook
define('AC_TABLET_DBLCLICK',"1");	// accion al hacer doble click en listado de manga 0:cancel&go 1:accept&go 

/** configuracion del sistema de acceso por internet */
define('AC_WEB_REFRESHTIME',"0");	// periodo de refresco en pantallas live
define('AC_PB_POLLTIME',5); 		// periodo de rotacion de logotipos de anuncios (segundos)
define('AC_PB_HDRFG1','#000000');	// colores de texto y fondo de cabecera y pie de pagina
define('AC_PB_HDRBG1','#FF7F00');
define('AC_PB_HDRFG2','#000000');	// colores de texto y fondo de informacion de ronda
define('AC_PB_HDRBG2','#FF7F00');
define('AC_PB_ROWCOLOR1','#ffffff'); // color de filas tablas principales
define('AC_PB_ROWCOLOR2','#e0ebff');
define('AC_PB_ROWCOLOR3','#ffffcf'); // color de filas tablas secundarias
define('AC_PB_ROWCOLOR4','#e0ebcf');

/** personalizacion del crono electronico */
define('AC_CRONO_RESYNC',"0");		// si crono manual continua (1) o restart (0) al paso por crono electronico
define('AC_CRONO_MILISECONDS',"0");	// presentar (1) milesimas o centesimas (0) de segundos
define('AC_CRONO_INTERMEDIATE',"0");// presentar (1) o no (0) datos de crono intermedio
define('AC_CRONO_RECTIME',"7");		// tiempo (minutos) de reconocimiento de pista (4..10)


Class Config {
	
	var $config=array();
	public static $locale_list= array( // stupid ms-windows :-(
		"es_ES" => Array('es_ES','es','es_ES.UTF-8','esp','spanish','spanish.1252'),
		"en_US" => Array('en_us','en','en_US.UTF-8','eng','english','english.1252'),
		"de_DE" => Array('de_DE','de','de_DE.UTF-8','ger','german','german.1252'),
		"es" => Array('es_ES','es','es_ES.UTF-8','esp','spanish','spanish.1252'),
		"en" => Array('en_us','en','en_US.UTF-8','eng','english','english.1252'),
		"de" => Array('de_DE','de','de_DE.UTF-8','ger','german','german.1252'),
		"es-ES" => Array('es_ES','es','es_ES.UTF-8','esp','spanish','spanish.1252'),
		"en-US" => Array('en_us','en','en_US.UTF-8','eng','english','english.1252'),
		"de-DE" => Array('de_DE','de','de_DE.UTF-8','ger','german','german.1252')
	);

	public static $config_options = array (
		/* name  => type system default */

		// version, logging y depuracion
		'debug_level' 		=> array(	'i',	false,	AC_DEBUG_LEVEL),
		'register_events'	=> array(	'b',	false,	AC_REGISTER_EVENTS),
		'reset_events'		=> array(	'b',	false,	AC_RESET_EVENTS),
		'event_printer'		=> array(	's',	false,	AC_EVENT_PRINTER),
		'wide_printer'		=> array(	'b',	false,	AC_WIDE_PRINTER),
		// Internacionalizacion. Idiomas
		'lang'				=> array(	's',	false,	AC_LANG),
		'accept_lang'		=> array(	'b',	false,	AC_ACCEPT_LANG),
		'proximity_alert'	=> array(	'i',	false,	AC_PROXIMITY_ALERT),
		// variables del sistema.
		// just declared, no neccesarily real value
		'restricted'		=> array(	'i',	true,	0),
		'version_name'		=> array(	's',	true,	"0.0.0"),
		'version_date'		=> array(	's',	true,	"20150101_0000"),
		'database_name'		=> array(	's',	true,	"name"),
		'database_host'		=> array(	's',	true,	"host"),
		'database_user'		=> array(	's',	true,	"user"),
		'database_pass'		=> array(	's',	true,	"pass"),
		'program_name'		=> array(	's',	true,	"Agilitycontest"),
		'author'			=> array(	's',	true,	"Juan Antonio Martinez"),
		'email'				=> array(	's',	true,	"juansgaviota@gmail.com"),
		'license'			=> array(	's',	true,	"GPL"),
		// entorno grafico
		'easyui_theme'		=> array(	's',	false,	AC_EASYUI_THEME),
		'easyui_bgcolor'	=> array(	'c',	false,	AC_EASYUI_BGCOLOR),
		'easyui_hdrcolor'	=> array(	'c',	false,	AC_EASYUI_HDRCOLOR),
		'easyui_opcolor'	=> array(	'c',	false,	AC_EASYUI_OPCOLOR),
		'easyui_rowcolor1'	=> array(	'c',	false,	AC_EASYUI_ROWCOLOR1),
		'easyui_rowcolor2'	=> array(	'c',	false,	AC_EASYUI_ROWCOLOR2),
		'easyui_rowcolor3'	=> array(	'c',	false,	AC_EASYUI_ROWCOLOR3),
		'easyui_rowcolor4'	=> array(	'c',	false,	AC_EASYUI_ROWCOLOR4),
		// configuracion del videowall
		'vw_polltime'		=> array(	'i',	false,	AC_VW_POLLTIME),
		'vw_alpha'			=> array(	'f',	false,	AC_VW_ALPHA),
		'vw_chromakey'		=> array(	'c',	false,	AC_VW_CRHOMAKEY),
		'vw_dataposition'	=> array(	'i',	false,	AC_VW_DATAPOSITION),
		'vw_hdrfg1'			=> array(	'c',	false,	AC_VW_HDRFG1),
		'vw_hdrbg1'			=> array(	'c',	false,	AC_VW_HDRBG1),
		'vw_hdrfg2'			=> array(	'c',	false,	AC_VW_HDRFG2),
		'vw_hdrbg2'			=> array(	'c',	false,	AC_VW_HDRBG2),
		'vw_hdrfg3'			=> array(	'c',	false,	AC_VW_HDRFG3),
		'vw_hdrbg3'			=> array(	'c',	false,	AC_VW_HDRBG3),
		'vw_rowcolor1'		=> array(	'c',	false,	AC_VW_ROWCOLOR1),
		'vw_rowcolor2'		=> array(	'c',	false,	AC_VW_ROWCOLOR2),
		'vw_rowcolor3'		=> array(	'c',	false,	AC_VW_ROWCOLOR3),
		'vw_rowcolor4'		=> array(	'c',	false,	AC_VW_ROWCOLOR4),
		// generacion de PDF's
		'pdf_topmargin'		=> array(	'i',	false,	AC_PDF_TOPMARGIN),
		'pdf_leftmargin'	=> array(	'i',	false,	AC_PDF_LEFTMARGIN),
		'pdf_labelheight'	=> array(	'i',	false,	AC_PDF_LABELHEIGHT),
		'pdf_journeys'		=> array(	'b',	false,	AC_PDF_JOURNEYS),
		'pdf_hdrfg1'		=> array(	'c',	false,	AC_PDF_HDRFG1),
		'pdf_hdrbg1'		=> array(	'c',	false,	AC_PDF_HDRBG1),
		'pdf_hdrfg2'		=> array(	'c',	false,	AC_PDF_HDRFG2),
		'pdf_hdrbg2'		=> array(	'c',	false,	AC_PDF_HDRBG2),
		'pdf_rowcolor1'		=> array(	'c',	false,	AC_PDF_ROWCOLOR1),
		'pdf_rowcolor2'		=> array(	'c',	false,	AC_PDF_ROWCOLOR2),
		'pdf_linecolor'		=> array(	'c',	false,	AC_PDF_LINECOLOR),
		'pdf_fontfamily'	=> array(	's',	false,	AC_PDF_FONTFAMILY),
		// personalizacion del tablet
		'tablet_beep'		=> array(	'b',	false,	AC_TABLET_BEEP),
		'tablet_dnd'		=> array(	'b',	false,	AC_TABLET_DND),
		'tablet_chrono'		=> array(	'b',	false,	AC_TABLET_CHRONO),
		'tablet_next'		=> array(	'b',	false,	AC_TABLET_NEXT),
		'tablet_countdown'	=> array(	'i',	false,	AC_TABLET_COUNTDOWN),
		'tablet_keyboard'	=> array(	'b',	false,	AC_TABLET_KEYBOARD),
		'tablet_dblclick'	=> array(	'i',	false,	AC_TABLET_DBLCLICK),
		// acceso web
		'web_refreshtime'	=> array(	'i',	false,	AC_WEB_REFRESHTIME),
		'pb_polltime'		=> array(	'i',	false,	AC_PB_POLLTIME),
		'pb_hdrfg1'			=> array(	'c',	false,	AC_VW_HDRFG1),
		'pb_hdrbg1'			=> array(	'c',	false,	AC_VW_HDRBG1),
		'pb_hdrfg2'			=> array(	'c',	false,	AC_VW_HDRFG2),
		'pb_hdrbg2'			=> array(	'c',	false,	AC_VW_HDRBG2),
		'pb_rowcolor1'		=> array(	'c',	false,	AC_PB_ROWCOLOR1),
		'pb_rowcolor2'		=> array(	'c',	false,	AC_PB_ROWCOLOR2),
		'pb_rowcolor3'		=> array(	'c',	false,	AC_PB_ROWCOLOR3),
		'pb_rowcolor4'		=> array(	'c',	false,	AC_PB_ROWCOLOR4),
		// personalizacion del crono
		'crono_resync'		=> array(	'b',	false,	AC_CRONO_RESYNC),
		'crono_miliseconds'	=> array(	'b',	false,	AC_CRONO_MILISECONDS),
		'crono_intermediate'=> array(	'b',	false,	AC_CRONO_INTERMEDIATE),
		'crono_rectime'		=> array(	'i',	false,	AC_CRONO_RECTIME)
	);

	// singleton pattern
	private static $instance=null;   
	public static function getInstance() {
		if (  !self::$instance instanceof self) self::$instance = new self;
		return self::$instance;
	}

	private function do_log($msg) {
		$stderr = fopen('php://stderr', 'w');
		fwrite($stderr,$msg);
		fclose($stderr);
	}

	function getPreferredLanguage($default) {
		$doIt=intval($this->config['accept_lang']);
		if ($doIt==0) return $default;
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return $default;
		$al=$_SERVER['HTTP_ACCEPT_LANGUAGE'];

		// regex inspired from @GabrielAnderson on http://stackoverflow.com/questions/6038236/http-accept-language
		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $al, $lang_parse);
		$langs = $lang_parse[1];
		$ranks = $lang_parse[4];

		// (create an associative array 'language' => 'preference')
		$lang2pref = array();
		for($i=0; $i<count($langs); $i++)
			$lang2pref[$langs[$i]] = (float) (!empty($ranks[$i]) ? $ranks[$i] : 1);

		// (comparison function for uksort)
		$cmpLangs = function ($a, $b) use ($lang2pref) {
			if ($lang2pref[$a] > $lang2pref[$b])		return -1;
			elseif ($lang2pref[$a] < $lang2pref[$b])	return 1;
			elseif (strlen($a) > strlen($b))			return -1;
			elseif (strlen($a) < strlen($b))			return 1;
			else return 0;
		};

		// sort the languages by prefered language and by the most specific region
		uksort($lang2pref, $cmpLangs);

		// return the first value's key
		reset($lang2pref);
		return key($lang2pref);
	}

	private function __construct() {

		// cargamos los valores por defecto
		foreach(Config::$config_options as $key => $info) {
			$this->config[$key]=$info[2];
		}
		// leemos fichero de sistema
		$sys=parse_ini_file(AC_SYSTEM_FILE,false); // false: don't parse subsections
		// leemos ahora el fichero de configuracion
		$res=parse_ini_file(AC_CONFIG_FILE,false); // false: don't parse subsections
		if ( ($res===FALSE) || ($sys===FALSE ) ){
			$this->config['configured'] =false; // mark initialization code to be executed
			return;
		}
		// cargamos los valores definidos en el fichero de configuracion
		foreach($this->config as $key => $val) {
			if ( array_key_exists($key,$res)) $this->config[$key]=$res[$key];
			if ( array_key_exists($key,$sys)) $this->config[$key]=$sys[$key];
		}

		// y ahora preparamos la internacionalizacion
		$windows=(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')?true:false;
		$locale=$this->getPreferredLanguage($this->config['lang']);

		$locales=Config::$locale_list[$locale];
		$sel=setlocale(LC_ALL, $locales);
		putenv("LC_ALL=$sel");
		// setlocale(LC_ALL, $locale);
		// putenv("LC_ALL=$locale");
        setlocale(LC_NUMERIC, ($windows)?'eng':'en_US'); // Fix for float number with incorrect decimal separator.
        $domain="AgilityContest";
		bindtextdomain($domain, __DIR__."/../../locale");
		if (!$windows) bind_textdomain_codeset($domain, 'UTF-8');
		textdomain($domain);
	}
	
	public function getEnv($key) {
		if (array_key_exists($key,$this->config)===FALSE) return null;
		return $this->config[$key];
	}
	
	public function setEnv($key,$value) {
		if (array_key_exists($key,$this->config)===FALSE) return;
		$this->config[$key]=$value;
	}
	
	/* from PHP documentation on parse_ini_file(); */
	private function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
		$content = "";
		if ($has_sections) {
			foreach ($assoc_arr as $key=>$elem) {
				$content .= "[".$key."]\n";
				foreach ($elem as $key2=>$elem2) {
					if(is_array($elem2)) {
						for($i=0;$i<count($elem2);$i++)	{
							$content .= $key2."[] = \"".$elem2[$i]."\"\n";
						}
					} else if($elem2=="") {
						$content .= $key2." = \n";
					} else {
						$content .= $key2." = \"".$elem2."\"\n";
					}
				}
			}
		} else {
			foreach ($assoc_arr as $key=>$elem) {
				if(is_array($elem))	{
					for($i=0;$i<count($elem);$i++) {
						$content .= $key."[] = \"".$elem[$i]."\"\n";
					}
				} else if ($elem==="") { // beware === to avoid conflict with "0" and "false" values
					$content .= $key." = \n"; // empty vars
				} else if (Config::$config_options[$key][1]==true) {
					continue; // skip system constants
				} else {
					$content .= $key." = \"$elem\"\n";
				}
			}
		}
		$handle = fopen($path, 'w');
        if (!is_resource($handle)) return false;
		$success = fwrite($handle, $content);
    	fclose($handle);
		// for windows (sucks) systems, also write to settings.bat to setup language
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $handle=fopen(AC_BATCH_FILE,'wb'); // force binary mode
            if (!is_resource($handle)) return false;
            fwrite($handle,"SET LANG=${assoc_arr['lang']}\r\n");
            fclose($handle);
        }
		return $success;
	}
	
	public function loadConfig() {
		// skip system.ini data
		$data=$this->config; // php copy by value, not by reference
		unset($data["database_name"]);
		unset($data["database_host"]);
		unset($data["database_user"]);
		unset($data["database_pass"]);
		unset($data["restricted"]);
		return $data;
	}
	
	function defaultConfig() {
		// cargamos los valores por defecto
		$data=array();
		foreach(Config::$config_options as $key => $info) {
			// no cargamos los datos de sistema
			if ($info[1]==false) $data[$key]=$info[2];
		}

		$result=$this->write_ini_file($data,AC_CONFIG_FILE);
		if ($result===FALSE) {
			$msg="Error al generar el fichero de configuracion";
			$this->do_log($msg);
			return $msg;
		}
		$this->config=array_merge($this->config,$data);
		return $data;
	}
	
	public function saveConfig() {
		do_log("hola mundo \n");
		$data=array();
		// notice that "ip_address" inputbox and "save" "restore" config buttons
		// are also received from console. just ignore it
		// search valid keys from http received parameters
		foreach(Config::$config_options as $key => $info) {
			if ($info[1]==true) continue; // ignoramos configuracion de sistema
			$type=$info[0];
			if ($info[0]=="c") $type="s";
			if ($info[0]=="b") $type="i";
			if (isset($_REQUEST[$key]))	$data[$key]=http_request($key,$type,$info[2]);
		}
		// finally write file:
		$res=array_merge($this->config,$data);
		$result=$this->write_ini_file($res,AC_CONFIG_FILE);
		if ($result===FALSE) return "Error al generar el fichero de configuracion";
		return "";
	}

	public function backupConfig() {
		$f=date("Ymd_Hi");
		$fd=fopen(AC_CONFIG_FILE,"r");
		if (!$fd) {
			setcookie('fileDownload','false',time()+30,"/");
			header("Cache-Control", "no-cache, no-store, must-revalidate");
		} else {
			$fsize = filesize(AC_CONFIG_FILE);
			setcookie('fileDownload','true',time()+30,"/");
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=config_$f.ini");
			header("Content-length: $fsize");
			header("Cache-control: private"); //use this to open files directly
			while(!feof($fd)) {
				$buffer = fread($fd, 2048);
				echo $buffer;
			}
			fclose ($fd);
		}
		return "ok";
	}

	public function restoreConfig() {
		// phase 1 retrieve data from browser
		// extraemos los datos de registro
		$data=http_request("Data","s",null);
		if (!$data) return array("errorMsg" => "restoreConfig()::download(): No data to import has been received");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
			return array("errorMsg" => "restoreConfig()::download() cannot handle data received from browser");
		}
		// mimetype for excel file is be stored at $matches[1]: and should be checked
		// $type=$matches[1]; // 'text/plain', or whatever. Not really used
		$contents= base64_decode( $matches[2] ); // decodes received data
		// phase 2 store it into temporary file
		$tmpfile=tempnam_sfx(__DIR__."/../../../logs","import","xlsx");
		$file=fopen($tmpfile,"wb");
		fwrite($file,$contents);
		fclose($file);
		$res=parse_ini_file($tmpfile,false); // don't parse subsections
		if (!$res) {
			return array("errorMsg" => "restoreConfig()::download() Received data is not a '.ini' file");
		}
		// phase 3 analyze data. skip internal vars, ignore unknown ones
		$result=array(); // create result string arrray to send to browser
		$data=array(); // create array to store valid entries
		foreach($res as $key => $value) {
			$key=strtolower($key);
			// si no existe se ignora
			if (!array_key_exists($key,Config::$config_options)) {
				array_push($result,"$key: Unknown. Ignored");
				continue;
			}
			// si es de sistema se ignora
			if (Config::$config_options[$key][1]==true) {
				array_push($result,"$key: Forbidden. Ignored");
				continue;
			}
			// check format
			$type="";
			switch(Config::$config_options[$key][0]) {
				case 's': if (is_string($value)) $type="string";break;
				case 'c': if (is_color($value)) $type="color";	break;
				case 'f': // trick to detect if an string contains a float
					$floatVal = floatval($value);
					if($floatVal && intval($floatVal) != $floatVal) $type="float";
					break;
				case 'i': if ( is_numeric($value) && (strpos($value,'.')==false) )$type="int"; break;
				case 'b': if (($value=="1") || ($value=="0")) $type="bool";	break;
			}
			if ($type=="") {
				array_push($result,"$key: Invalid format. Ignored");
				continue;
			}
			// arriving here means data is valid
			// array_push($result,"$key: ($type) $value Accepted");
			$data[$key]=$value;
		}
		// finally write file:
		$res=array_merge($this->config,$data);
		$wres=$this->write_ini_file($res,AC_CONFIG_FILE);
		if ($wres===FALSE) return array("errorMsg" => "restoreConfig()::save() error saving .ini file");
		return array('data'=>join('<br />',$result));
	}
}

?>