<?php 
/*
 Config.php

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

/** default values **/
define('AC_CONFIG_FILE',__DIR__."/config.ini"); // user definable configuration
define('AC_SYSTEM_FILE',__DIR__."/system.ini"); // system configuration.

/** Internacionalizacion. Idiomas **/
define ('AC_LANG','es_ES');

/** logging **/
define('AC_DEBUG_LEVEL',0);
define('AC_REGISTER_EVENTS',"0");
define('AC_RESET_EVENTS',"1");

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

/** personalizacion del videowall **/
define('AC_VW_POLLTIME',5);
define('AC_VW_ALPHA',0.5);
define('AC_VW_HDRFG1','#000000');
define('AC_VW_HDRBG1','#FF7F00');
define('AC_VW_HDRFG2','#0000ff');
define('AC_VW_HDRBG2','#808080');
define('AC_VW_HDRFG3','#000000');
define('AC_VW_HDRBG3','#808080');
define('AC_VW_ROWCOLOR1','#ffffff');
define('AC_VW_ROWCOLOR2','#e0ebff');

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

/** personalizacion del tablet **/
define('AC_TABLET_BEEP',"0");
define('AC_TABLET_DND',"0");
define('AC_TABLET_CHRONO',"0");
define('AC_TABLET_NEXT',"0");
define('AC_TABLET_COUNTDOWN',"1"); // 1:nada 2:crono 3:eliminado

Class Config {
	
	var $config=array();

	public static $locale_list= array( // stupid ms-windows :-(
		"es_ES" => Array('es_ES','es','es_ES.UTF-8','esp','spanish','spanish.1252'),
		"en_US" => Array('en_us','en','en_US.UTF-8','eng','english','english.1252')
	);

	// singleton pattern
	private static $instance=null;   
	public static function getInstance() {
		if (  !self::$instance instanceof self) self::$instance = new self;
		return self::$instance;
	}
	
	private function __construct() {

		/** cargamos los valores por defecto **/

        // General
		// version, logging y depuracion
		$this->config['debug_level'] =	AC_DEBUG_LEVEL;
        $this->config['register_events'] =	AC_REGISTER_EVENTS;
        $this->config['reset_events'] =	AC_RESET_EVENTS;
		// Internacionalizacion. Idiomas
		$this->config['lang'] =	AC_LANG;
        $this->config['proximity_alert'] =	AC_PROXIMITY_ALERT;

		// variables del sistema.
        // just declared, no neccesarily real value
        $this->config['restricted']     =	0;
        $this->config['version_name']   =	"0.0";
        $this->config['version_date']   =	"20150101_0000";
		$this->config['database_name']  =	"agility";
		$this->config['database_host']  =	"localhost";
		$this->config['database_user']  =	"user";
		$this->config['database_pass']  = 	"password";
        $this->config['program_name']   = 	"Agilitycontest";
        $this->config['author'] = 	"Juan Antonio Martinez";
        $this->config['email'] = 	"juansgaviota@gmail.com";
        $this->config['license'] = 	"GPL";

		// entorno grafico
		$this->config['easyui_theme'] = 	AC_EASYUI_THEME;
		$this->config['easyui_bgcolor'] =	AC_EASYUI_BGCOLOR;
		$this->config['easyui_hdrcolor'] =	AC_EASYUI_HDRCOLOR;
		$this->config['easyui_opcolor'] =	AC_EASYUI_OPCOLOR;
		$this->config['easyui_rowcolor1'] =	AC_EASYUI_ROWCOLOR1;
		$this->config['easyui_rowcolor2'] =	AC_EASYUI_ROWCOLOR2;
		$this->config['easyui_rowcolor3'] =	AC_EASYUI_ROWCOLOR3;
		// configuracion del videowall
		$this->config['vw_polltime'] =	AC_VW_POLLTIME;
		$this->config['vw_alpha'] =		AC_VW_ALPHA;
		$this->config['vw_hdrfg1'] =	AC_VW_HDRFG1;
		$this->config['vw_hdrbg1'] =	AC_VW_HDRBG1;
        $this->config['vw_hdrfg2'] =	AC_VW_HDRFG2;
        $this->config['vw_hdrbg2'] =	AC_VW_HDRBG2;
        $this->config['vw_hdrfg3'] =	AC_VW_HDRFG3;
        $this->config['vw_hdrbg3'] =	AC_VW_HDRBG3;
		$this->config['vw_rowcolor1'] =	AC_VW_ROWCOLOR1;
		$this->config['vw_rowcolor2'] =	AC_VW_ROWCOLOR2;
		// generacion de PDF's
		$this->config['pdf_topmargin'] = AC_PDF_TOPMARGIN;
		$this->config['pdf_leftmargin'] = AC_PDF_LEFTMARGIN;
		$this->config['pdf_labelheight'] = AC_PDF_LABELHEIGHT;
		$this->config['pdf_journeys'] = AC_PDF_JOURNEYS;
		$this->config['pdf_hdrfg1'] =	AC_PDF_HDRFG1;
		$this->config['pdf_hdrbg1'] =	AC_PDF_HDRBG1;
		$this->config['pdf_hdrfg2'] =	AC_PDF_HDRFG2;
		$this->config['pdf_hdrbg2'] =	AC_PDF_HDRBG2;
		$this->config['pdf_rowcolor1'] =	AC_PDF_ROWCOLOR1;
		$this->config['pdf_rowcolor2'] =	AC_PDF_ROWCOLOR2;
		$this->config['pdf_linecolor'] =	AC_PDF_LINECOLOR;
		
		// personalizacion del tablet
		$this->config['tablet_beep'] =	AC_TABLET_BEEP;
		$this->config['tablet_dnd'] =	AC_TABLET_DND;
		$this->config['tablet_chrono'] =	AC_TABLET_CHRONO;
		$this->config['tablet_next'] =	AC_TABLET_NEXT;
		$this->config['tablet_countdown'] =	AC_TABLET_COUNTDOWN;

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
		$locale=$this->config['lang'];
		$locales=Config::$locale_list[$locale];
		$sel=setlocale(LC_ALL, $locales);
		putenv("LC_ALL=$sel");
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
				} else if($elem=="") {
					$content .= $key." = \n";
				} else {
					// skip system constants
					switch ($key) {
					case "restricted": break;
					case "program_name": break;
					case "author": break;
					case "email": break;
					case "license": break;
					case "version_name": break;
					case "version_date": break;
					case "database_name": break;
					case "database_host": break;
					case "database_user": break;
					case "database_pass": break;
					default: $content .= $key." = \"".$elem."\"\n"; break;
					}

				}
			}
		}
		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		$success = fwrite($handle, $content);
    	fclose($handle);
		return $success;
	}
	
	public function loadConfig() {
		return $this->config;
	}
	
	function defaultConfig() {
		$data=array();

		// skip version info/date as cannot be edited by user

		$this->config['debug_level'] =	AC_DEBUG_LEVEL;
        $this->config['register_events'] =	AC_REGISTER_EVENTS;
        $this->config['reset_events'] =	AC_RESET_EVENTS;
        $this->config['proximity_alert'] =	AC_PROXIMITY_ALERT;
		
		// configuracion de la consola
		$data['easyui_theme'] = 	AC_EASYUI_THEME;
		$data['easyui_bgcolor'] =	AC_EASYUI_BGCOLOR;
		$data['easyui_hdrcolor'] =	AC_EASYUI_HDRCOLOR;
		$data['easyui_opcolor'] =	AC_EASYUI_OPCOLOR;
		$data['easyui_rowcolor1'] =	AC_EASYUI_ROWCOLOR1;
		$data['easyui_rowcolor2'] =	AC_EASYUI_ROWCOLOR2;
		$data['easyui_rowcolor3'] =	AC_EASYUI_ROWCOLOR3;
		// configuracion del videowall
		$data['vw_polltime'] =	AC_VW_POLLTIME;
		$data['vw_alpha'] =		AC_VW_ALPHA;
		$data['vw_hdrfg1'] =	AC_VW_HDRFG1;
		$data['vw_hdrbg1'] =	AC_VW_HDRBG1;
        $data['vw_hdrfg2'] =	AC_VW_HDRFG2;
        $data['vw_hdrbg2'] =	AC_VW_HDRBG2;
        $data['vw_hdrfg3'] =	AC_VW_HDRFG3;
        $data['vw_hdrbg3'] =	AC_VW_HDRBG3;
		$data['vw_rowcolor1'] =	AC_VW_ROWCOLOR1;
		$data['vw_rowcolor2'] =	AC_VW_ROWCOLOR2;
		// generacion de PDF's
		$data['pdf_topmargin'] = AC_PDF_TOPMARGIN;
		$data['pdf_leftmargin'] = AC_PDF_LEFTMARGIN;
		$data['pdf_labelheight'] = AC_PDF_LABELHEIGHT;
		$data['pdf_journeys'] = AC_PDF_JOURNEYS;
		$data['pdf_hdrfg1'] =	AC_PDF_HDRFG1;
		$data['pdf_hdrbg1'] =	AC_PDF_HDRBG1;
		$data['pdf_hdrfg2'] =	AC_PDF_HDRFG2;
		$data['pdf_hdrbg2'] =	AC_PDF_HDRBG2;
		$data['pdf_rowcolor1'] =	AC_PDF_ROWCOLOR1;
		$data['pdf_rowcolor2'] =	AC_PDF_ROWCOLOR2;
		$data['pdf_linecolor'] =	AC_PDF_LINECOLOR;
		// tablet
		$this->config['tablet_beep'] =	AC_TABLET_BEEP;
		$this->config['tablet_dnd'] =	AC_TABLET_DND;
		$this->config['tablet_chrono'] =	AC_TABLET_CHRONO;
		$this->config['tablet_next'] =	AC_TABLET_NEXT;
		$this->config['tablet_countdown'] =	AC_TABLET_COUNTDOWN;
		// Internacionalizacion. Idiomas
		$data['lang'] =	AC_LANG;
		$res=array_merge($this->config,$data);
		$result=$this->write_ini_file($res,AC_CONFIG_FILE);
		if ($result===FALSE) return "Error al generar el fichero de configuracion";
		$this->config=$res;
		return $res;
	}
	
	public function saveConfig() {
		$data=array();
		
		// entorno grafico
		$data=testAndSet($data,'easyui_theme','s',AC_EASYUI_THEME);
		$data=testAndSet($data,'easyui_bgcolor','s',AC_EASYUI_BGCOLOR);
		$data=testAndSet($data,'easyui_hdrcolor','s',AC_EASYUI_HDRCOLOR);
		$data=testAndSet($data,'easyui_opcolor','s',AC_EASYUI_OPCOLOR);
		$data=testAndSet($data,'easyui_rowcolor1','s',AC_EASYUI_ROWCOLOR1);
		$data=testAndSet($data,'easyui_rowcolor2','s',AC_EASYUI_ROWCOLOR2);
		$data=testAndSet($data,'easyui_rowcolor3','s',AC_EASYUI_ROWCOLOR3);
		
		// configuracion del videowall
		$data=testAndSet($data,'vw_polltime','i',AC_VW_POLLTIME);
		$data=testAndSet($data,'vw_alpha','f',AC_VW_ALPHA);
		$data=testAndSet($data,'vw_hdrfg1','s',AC_VW_HDRFG1);
		$data=testAndSet($data,'vw_hdrbg1','s',AC_VW_HDRBG1);
        $data=testAndSet($data,'vw_hdrfg2','s',AC_VW_HDRFG2);
        $data=testAndSet($data,'vw_hdrbg2','s',AC_VW_HDRBG2);
        $data=testAndSet($data,'vw_hdrfg3','s',AC_VW_HDRFG3);
        $data=testAndSet($data,'vw_hdrbg3','s',AC_VW_HDRBG3);
		$data=testAndSet($data,'vw_rowcolor1','s',AC_VW_ROWCOLOR1);
		$data=testAndSet($data,'vw_rowcolor2','s',AC_VW_ROWCOLOR2);
		
		// generacion de PDF's
		$data=testAndSet($data,'pdf_topmargin','f',AC_PDF_TOPMARGIN);
		$data=testAndSet($data,'pdf_leftmargin','f',AC_PDF_LEFTMARGIN);
		$data=testAndSet($data,'pdf_labelheight','f',AC_PDF_LABELHEIGHT);
		$data['pdf_journeys']=http_request('pdf_journeys','i',AC_PDF_JOURNEYS);
		$data=testAndSet($data,'pdf_hdrfg1','s',AC_PDF_HDRFG1);
		$data=testAndSet($data,'pdf_hdrbg1','s',AC_PDF_HDRBG1);
		$data=testAndSet($data,'pdf_hdrfg2','s',AC_PDF_HDRFG2);
		$data=testAndSet($data,'pdf_hdrbg2','s',AC_PDF_HDRBG2);
		$data=testAndSet($data,'pdf_rowcolor1','s',AC_PDF_ROWCOLOR1);
		$data=testAndSet($data,'pdf_rowcolor2','s',AC_PDF_ROWCOLOR2);
		$data=testAndSet($data,'pdf_linecolor','s',AC_PDF_LINECOLOR);
		
		// tablet
		$data['tablet_beep']=http_request('tablet_beep','s',AC_TABLET_BEEP);
		$data['tablet_dnd']=http_request('tablet_dnd','s',AC_TABLET_DND);
		$data['tablet_chrono']=http_request('tablet_chrono','s',AC_TABLET_CHRONO);
		$data['tablet_next']=http_request('tablet_chrono','s',AC_TABLET_NEXT);
		$data['tablet_countdown']=http_request('tablet_countdown','i',AC_TABLET_COUNTDOWN);
		
		// Sistema
		$data=testAndSet($data,'lang','s',AC_LANG);
		$data=testAndSet($data,'debug_level','i',AC_DEBUG_LEVEL);
        $data['register_events']=http_request('register_events','s',AC_REGISTER_EVENTS);
        $data['reset_events']=http_request('reset_events','s',AC_RESET_EVENTS);
        $data=testAndSet($data,'proximity_alert','i',AC_PROXIMITY_ALERT);
		
		// finally write file:
		$res=array_merge($this->config,$data);
		$result=$this->write_ini_file($res,AC_CONFIG_FILE);
		if ($result===FALSE) return "Error al generar el fichero de configuracion";
		return "";
	}
}

?>