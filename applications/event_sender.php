<?php
/*
 Config.php

 Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
define('AC_CONFIG_FILE', __DIR__ . "/../config/config.ini"); // user definable configuration
define('AC_SYSTEM_FILE', __DIR__ . "/../config/system.ini"); // system configuration.

/** running modes */
define('AC_RUNMODE_STANDALONE',1);	// normal (pc/mac/linux) client installation
define('AC_RUNMODE_SHARED',2);		// shared (webhost) client install
define('AC_RUNMODE_SLAVE',4);			// slave (replicated) database server mode
define('AC_RUNMODE_MASTER',8);		// master server installation
define('AC_RUNMODE_EVTSOURCE', AC_RUNMODE_STANDALONE | AC_RUNMODE_SHARED ); // can be used as event source

/** Internacionalizacion. Idiomas **/
define('AC_LANG','es_ES');
define('AC_ACCEPT_LANG','0');

/** Copia de seguridad automatica **/
define('AC_BACKUP_DISABLED','0'); // default is perform auto-backups
define('AC_BACKUP_PERIOD','0');
define('AC_BACKUP_DIR',"");
define('AC_BACKUP_DOGS',"0");
define('AC_CRYPT_DB',"0"); // do not crypt database backup contents

/** logging **/
define('AC_DEBUG_LEVEL','0');
define('AC_REGISTER_EVENTS',"0"); // write events into file
define('AC_REMOTE_EVENTS_URL',""); // URL to send events outside server. format: http(s)://{host}:{port}/{baseurl}
define('AC_CONSOLE_EVENTS',"0"); // track and show events in cosole
define('AC_CONSOLE_NEWS',"1"); // ask server for news on startup
define('AC_RESET_EVENTS',"1"); // clear event registry on login
define('AC_SEARCH_UPDATES',"1"); // look for application updates at startup
define('AC_SEARCH_UPDATEDB',"-1"); // look for application updates at startup -1:ask, 0:don't 1:do
define('AC_FULL_BACKUP',"0"); // include events in database backup
define('AC_EVENT_PRINTER','');
define('AC_WIDE_PRINTER','0'); // defaults to 58mm wide POS printer

/** variables de la aplicacion principal **/
define('AC_PROXIMITY_ALERT',10);
define('AC_TRAINING_TIME',180);
define('AC_TRAINING_TYPE',0);
define('AC_TRAINING_GRACE',15);

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

// personalizacion del live streaming
define('AC_LS_HDRFG1','#000000');
define('AC_LS_HDRBG1','#FF7F00');
define('AC_LS_HDRFG2','#0000ff');
define('AC_LS_HDRBG2','#808080');
define('AC_LS_HDRFG3','#000000');
define('AC_LS_HDRBG3','#808080');
define('AC_LS_ROWCOLOR1','#ffffff');
define('AC_LS_ROWCOLOR2','#e0ebff');
define('AC_LS_ROWCOLOR3','#ffffcf');
define('AC_LS_ROWCOLOR4','#e0ebcf');
define('AC_LS_ALPHA',0.5);
define('AC_LS_EVTDELAY',1.0);
define('AC_LS_CRHOMAKEY','#00ff00');
define('AC_LS_DATAPOSITION',1); // 0:hidden 1:top/right 2:down/rignt 3:down/center
define('AC_LS_INFOPOSITION',1); // 0:hidden 1:top/left 2:on top of dog info
define('AC_LS_TOBEFIRST',1); // enable evaluate time to get first place ( computing time consumer )

/** personalizacion del videowall simplificado **/
define('AC_VWS_POLLTIME',5);
define('AC_VWS_USELOGO',5);
define('AC_VWS_LOGOURL',"../images/agilityawc2016.png");
define('AC_VWS_ANIMATION',1);
define('AC_VWS_FONT','futura_condensedbold');
define('AC_VWS_FONTSIZE','2.5');
define('AC_VWS_HDRFG1','#FFFFFF');
define('AC_VWS_HDRBG1','#FF7F00');
define('AC_VWS_HDRFG2','#000000');
define('AC_VWS_HDRBG2','#404040');
define('AC_VWS_ROWCOLOR1','#7f7f7f');
define('AC_VWS_ROWCOLOR2','#606b7f');
define('AC_VWS_ROWCOLOR3','#ffffcf');
define('AC_VWS_ROWCOLOR4','#606b4f');
define('AC_VWS_ROWCOLOR5','#FFFFFF');
define('AC_VWS_ROWCOLOR6','#808080');
define('AC_VWS_LINECOLOR','#ffffff');

/** generacion de PDF's **/
define('AC_PDF_SKIPNPEL', "1"); // which labels to print 0:All 1:Skip Elim-NP 2:Only Excelents
define('AC_PDF_SKIPPA', "1"); // skip print dorsal labels for pre-agility competitors (default)
define('AC_PDF_TOPMARGIN', 10.0); // margen superior etiquetas
define('AC_PDF_LEFTMARGIN', 10.0); // margen izquierdo etiquetas
define('AC_PDF_LABELHEIGHT', 17.0); // Altura de las pegatinas mmts
define('AC_PDF_JOURNEYS', "1"); // incluir jornadas en catalogo
define('AC_PDF_GRADES', "1"); // incluir informacion de grado en catalogo
define('AC_PDF_CATHANDLERS', "1"); // incluir informacion categoria del guia en los listados
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
define('AC_PB_ROTATELOGOS',5); 		// periodo de rotacion de logotipos de anuncios (segundos)
define('AC_PB_HDRFG1','#000000');	// colores de texto y fondo de cabecera y pie de pagina
define('AC_PB_HDRBG1','#FF7F00');
define('AC_PB_HDRFG2','#000000');	// colores de texto y fondo de informacion de ronda
define('AC_PB_HDRBG2','#FF7F00');
define('AC_PB_ROWCOLOR1','#ffffff'); // color de filas tablas principales
define('AC_PB_ROWCOLOR2','#e0ebff');
define('AC_PB_ROWCOLOR3','#ffffcf'); // color de filas tablas secundarias
define('AC_PB_ROWCOLOR4','#e0ebcf');
define('AC_PB_ROWCOLOR5','#e0d0e0'); // color de la fila del perro cuyo dorsal esta seleccionado

/** personalizacion del crono electronico */
define('AC_CRONO_RESYNC',"0");		// si crono manual continua (1) o restart (0) al paso por crono electronico
define('AC_CRONO_MILLISECONDS',"0");	// presentar (1) milesimas o centesimas (0) de segundos
define('AC_CRONO_INTERMEDIATE',"0");// presentar (1) o no (0) datos de crono intermedio
define('AC_CRONO_RECTIME',"7");		// tiempo (minutos) de reconocimiento de pista (4..10)

/** configuracion de pruebas */
define('AC_TEAM4_MODE',"0"); // 0->100+tiempo corre 1->100+tiempo=TRM 2->EquipoEliminado
define('AC_GAMBLER_SEQ1',"10"); // puntos de la secuencia corta del gambler
define('AC_GAMBLER_SEQ2',"20"); // puntos de la secuencia corta del gambler
define('AC_GAMBLER_BONUS1',"6"); // puntos de bonus adicionales 1
define('AC_GAMBLER_BONUS2',"7"); // puntos de bonus adicionales 2
define('AC_GAMBLER_BONUS3',"8"); // puntos de bonus adicionales 3
define('AC_GAMBLER_BONUS4',"9"); // puntos de bonus adicionales 4
define('AC_GAMBLER_BONUS5',"10"); // puntos de bonus adicionales 5
define('AC_GAMBLER_EXTRA',"10"); // puntos de bonus secuencia extra

/** datos de correo electronico defaults are for google */
define('AC_EMAIL_SERVER',"smtp.gmail.com"); // SMTP Server name
define('AC_EMAIL_PORT',587); // SMTP Server port
define('AC_EMAIL_CRYPT',"STARTTLS"); // encryption method
define('AC_EMAIL_AUTH',"LOGIN"); // auth method
define('AC_EMAIL_USER',""); // account user name
define('AC_EMAIL_PASS',""); // base64 encoded password ( sucks, sure, but better than nothing )
define('AC_EMAIL_REALM',""); // realm domain for ntlm auth
define('AC_EMAIL_WORKSTATION',""); // domain workstation for ntlm auth

Class Config {

    protected $config=array();

    public static $config_options = array (
        /* name  => type system default */

        // version, logging y depuracion
        'debug_level' 		=> array(	'i',	false,	AC_DEBUG_LEVEL),
        'register_events'	=> array(	'b',	false,	AC_REGISTER_EVENTS),
        'remote_events_url'=> array(	's',	false,	AC_REMOTE_EVENTS_URL),
        'reset_events'		=> array(	'b',	false,	AC_RESET_EVENTS),
        'console_events'	=> array(	'b',	false,	AC_CONSOLE_EVENTS),
        'console_news'		=> array(	'i',	false,	AC_CONSOLE_NEWS),
        'event_printer'		=> array(	's',	false,	AC_EVENT_PRINTER),
        'full_backup'		=> array(	'i',	false,	AC_FULL_BACKUP),
        'search_updates' 	=> array(	'b',	false,	AC_SEARCH_UPDATES),
        'search_updatedb' 	=> array(	'i',	false,	AC_SEARCH_UPDATEDB),
        'wide_printer'		=> array(	'b',	false,	AC_WIDE_PRINTER),
        // Internacionalizacion
        'lang'				=> array(	's',	false,	AC_LANG),
        'accept_lang'		=> array(	'b',	false,	AC_ACCEPT_LANG),
        // backups
        'backup_disabled'	=> array(	'i',	false,	AC_BACKUP_DISABLED),
        'backup_period'		=> array(	'i',	false,	AC_BACKUP_PERIOD),
        'backup_dir'		=> array(	's',	false,	AC_BACKUP_DIR),
        'backup_dogs'		=> array(	'i',	false,	AC_BACKUP_DOGS),
        'encrypt_database'	=> array(	'i',	false,	AC_CRYPT_DB),

        // configuracion de la prueba
        'proximity_alert'	=> array(	'i',	false,	AC_PROXIMITY_ALERT),
        'training_time'		=> array(	'i',	false,	AC_TRAINING_TIME),
        'training_type'		=> array(	'i',	false,	AC_TRAINING_TYPE),
        'training_grace'	=> array(	'i',	false,	AC_TRAINING_GRACE),

        // variables del sistema.
        // here comes default values. real ones should be extracted from system.ini file
        'running_mode'		=> array(	's',	true,	AC_RUNMODE_STANDALONE),
        'version_name'		=> array(	's',	true,	"0.0.0"),
        'version_date'		=> array(	's',	true,	"00000000_0000"),
        'database_name'		=> array(	's',	true,	"dbname"),
        'database_host'		=> array(	's',	true,	"dbhost"),
        'database_user'		=> array(	's',	true,	"dbuser"),
        'database_pass'		=> array(	's',	true,	"dbpass"),
        'database_ruser'	=> array(	's',	true,	"dbruser"),
        'database_rpass'	=> array(	's',	true,	"dbrpass"),
        'program_name'		=> array(	's',	true,	"Agilitycontest"),
        'author'			=> array(	's',	true,	"Juan Antonio Martinez"),
        'email'				=> array(	's',	true,	"juansgaviota@gmail.com"),
        'license'			=> array(	's',	true,	"GPL"),
        'uniqueID'			=> array(   's',	true,	""),
        'master_server'		=> array(	's',	true,	"www.agilitycontest.es"),
        'master_baseurl'	=> array(	's',	true,	"agility"),

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

        // configuracion del live stream
        'ls_hdrfg1'			=> array(	'c',	false,	AC_LS_HDRFG1),
        'ls_hdrbg1'			=> array(	'c',	false,	AC_LS_HDRBG1),
        'ls_hdrfg2'			=> array(	'c',	false,	AC_LS_HDRFG2),
        'ls_hdrbg2'			=> array(	'c',	false,	AC_LS_HDRBG2),
        'ls_hdrfg3'			=> array(	'c',	false,	AC_LS_HDRFG3),
        'ls_hdrbg3'			=> array(	'c',	false,	AC_LS_HDRBG3),
        'ls_rowcolor1'		=> array(	'c',	false,	AC_LS_ROWCOLOR1),
        'ls_rowcolor2'		=> array(	'c',	false,	AC_LS_ROWCOLOR2),
        'ls_rowcolor3'		=> array(	'c',	false,	AC_LS_ROWCOLOR3),
        'ls_rowcolor4'		=> array(	'c',	false,	AC_LS_ROWCOLOR4),
        'ls_tobefirst'		=> array(	'i',	false,	AC_LS_TOBEFIRST),
        'ls_alpha'			=> array(	'f',	false,	AC_LS_ALPHA),
        'ls_evtdelay'		=> array(	'f',	false,	AC_LS_EVTDELAY),
        'ls_chromakey'		=> array(	'c',	false,	AC_LS_CRHOMAKEY),
        'ls_dataposition'	=> array(	'i',	false,	AC_LS_DATAPOSITION),
        'ls_infoposition'	=> array(	'i',	false,	AC_LS_INFOPOSITION),

        // configuracion del simplified videowall
        'vws_polltime'		=> array(	'i',	false,	AC_VWS_POLLTIME),
        'vws_uselogo'		=> array(	'i',	false,	AC_VWS_USELOGO),
        'vws_logourl'		=> array(	's',	false,	AC_VWS_LOGOURL),
        'vws_animation'		=> array(	'i',	false,	AC_VWS_ANIMATION),
        'vws_font'			=> array(	's',	false,	AC_VWS_FONT),
        'vws_fontsize'		=> array(	'f',	false,	AC_VWS_FONTSIZE),
        'vws_hdrfg1'		=> array(	'c',	false,	AC_VWS_HDRFG1),
        'vws_hdrbg1'		=> array(	'c',	false,	AC_VWS_HDRBG1),
        'vws_hdrfg2'		=> array(	'c',	false,	AC_VWS_HDRFG2),
        'vws_hdrbg2'		=> array(	'c',	false,	AC_VWS_HDRBG2),
        'vws_rowcolor1'		=> array(	'c',	false,	AC_VWS_ROWCOLOR1),
        'vws_rowcolor2'		=> array(	'c',	false,	AC_VWS_ROWCOLOR2),
        'vws_rowcolor3'		=> array(	'c',	false,	AC_VWS_ROWCOLOR3),
        'vws_rowcolor4'		=> array(	'c',	false,	AC_VWS_ROWCOLOR4),
        'vws_rowcolor5'		=> array(	'c',	false,	AC_VWS_ROWCOLOR5),
        'vws_rowcolor6'		=> array(	'c',	false,	AC_VWS_ROWCOLOR6),
        'vws_linecolor'		=> array(	'c',	false,	AC_VWS_LINECOLOR),
        // generacion de PDF's
        'pdf_skipnpel'		=> array(	'i',	false,	AC_PDF_SKIPNPEL),
        'pdf_skippa'		=> array(	'i',	false,	AC_PDF_SKIPPA),
        'pdf_topmargin'		=> array(	'i',	false,	AC_PDF_TOPMARGIN),
        'pdf_leftmargin'	=> array(	'i',	false,	AC_PDF_LEFTMARGIN),
        'pdf_labelheight'	=> array(	'i',	false,	AC_PDF_LABELHEIGHT),
        'pdf_journeys'		=> array(	'b',	false,	AC_PDF_JOURNEYS),
        'pdf_grades'		=> array(	'b',	false,	AC_PDF_GRADES),
        'pdf_cathandlers'	=> array(	'b',	false,	AC_PDF_CATHANDLERS),
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
        'pb_rotatelogos'	=> array(	'i',	false,	AC_PB_ROTATELOGOS),
        'pb_hdrfg1'			=> array(	'c',	false,	AC_PB_HDRFG1),
        'pb_hdrbg1'			=> array(	'c',	false,	AC_PB_HDRBG1),
        'pb_hdrfg2'			=> array(	'c',	false,	AC_PB_HDRFG2),
        'pb_hdrbg2'			=> array(	'c',	false,	AC_PB_HDRBG2),
        'pb_rowcolor1'		=> array(	'c',	false,	AC_PB_ROWCOLOR1),
        'pb_rowcolor2'		=> array(	'c',	false,	AC_PB_ROWCOLOR2),
        'pb_rowcolor3'		=> array(	'c',	false,	AC_PB_ROWCOLOR3),
        'pb_rowcolor4'		=> array(	'c',	false,	AC_PB_ROWCOLOR4),
        'pb_rowcolor5'		=> array(	'c',	false,	AC_PB_ROWCOLOR5),
        // personalizacion del crono
        'crono_resync'		=> array(	'b',	false,	AC_CRONO_RESYNC),
        'crono_milliseconds'=> array(	'b',	false,	AC_CRONO_MILLISECONDS),
        'crono_intermediate'=> array(	'b',	false,	AC_CRONO_INTERMEDIATE),
        'crono_rectime'		=> array(	'i',	false,	AC_CRONO_RECTIME),

        // personalizacion de competiciones
        'team4_mode'		=> array(	'i',	false,	AC_TEAM4_MODE),
        'gambler_seq1'		=> array(	'i',	false,	AC_GAMBLER_SEQ1),
        'gambler_seq2'		=> array(	'i',	false,	AC_GAMBLER_SEQ2),
        'gambler_bonus1'	=> array(	'i',	false,	AC_GAMBLER_BONUS1),
        'gambler_bonus2'	=> array(	'i',	false,	AC_GAMBLER_BONUS2),
        'gambler_bonus3'	=> array(	'i',	false,	AC_GAMBLER_BONUS3),
        'gambler_bonus4'	=> array(	'i',	false,	AC_GAMBLER_BONUS4),
        'gambler_bonus5'	=> array(	'i',	false,	AC_GAMBLER_BONUS5),
        'gambler_extra'		=> array(	'i',	false,	AC_GAMBLER_EXTRA),

        // correo electronico
        'email_server'		=> array(	's',	false,	AC_EMAIL_SERVER),
        'email_port'		=> array(	'i',	false,	AC_EMAIL_PORT),
        'email_crypt'		=> array(	's',	false,	AC_EMAIL_CRYPT),
        'email_auth'		=> array(	's',	false,	AC_EMAIL_AUTH),
        'email_user'		=> array(	's',	false,	AC_EMAIL_USER),
        'email_pass'		=> array(	's',	false,	AC_EMAIL_PASS),
        'email_realm'		=> array(	's',	false,	AC_EMAIL_REALM),
        'email_workstation'	=> array(	's',	false,	AC_EMAIL_WORKSTATION)
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

    private function readAC_configFile($file) {
        if (!file_exists($file)) return array();
        $data=parse_ini_file($file,false); // use false to don't handle subsections
        foreach ($data as $key => $val) {
            // transcode special fields
            if ($key==="email_user") $data[$key]=base64_decode($val);
            if ($key==="email_pass") $data[$key]=base64_decode($val);
        }
        return $data;
    }

    private function __construct() {

        // cargamos los valores por defecto
        foreach(Config::$config_options as $key => $info) {
            $this->config[$key]=$info[2];
        }
        // leemos fichero de sistema
        $sys=$this->readAC_configFile(AC_SYSTEM_FILE);
        // leemos ahora el fichero de configuracion
        $res=$this->readAC_configFile(AC_CONFIG_FILE);
        if ( ($res===FALSE) || ($sys===FALSE ) ){
            $this->config['configured'] =false; // mark initialization code to be executed
            return;
        }
        foreach($this->config as $key => $val) {
            // cargamos los valores definidos en el fichero de configuracion
            if ( array_key_exists($key,$res)) $this->config[$key]=$res[$key];
            // ahora procesamos los datos del sistema, que tienen precedencia
            if ( array_key_exists($key,$sys)) $this->config[$key]=$sys[$key];
        }

    }

    public function getEnv($key) {
        if (array_key_exists($key,$this->config)===FALSE) return null;
        return $this->config[$key];
    }

    public function setEnv($key,$value) {
        if (array_key_exists($key,$this->config)===FALSE) return;
        $this->config[$key]=$value;
    }

    public function getConfig() {
        return $this->config;
    }

}

/*
logging.php

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

define ("LEVEL_PANIC",0);
define ("LEVEL_ALERT",1);
define ("LEVEL_ERROR",2);
define ("LEVEL_WARN",3);
define ("LEVEL_NOTICE",4);
define ("LEVEL_INFO",5);
define ("LEVEL_DEBUG",6);
define ("LEVEL_TRACE",7);

define ("LEVEL_ALL",8);
define ("LEVEL_NONE",-1);

class Logger {
    private $basename;
    private $level;
    private static $levels= array("PANIC","ALERT","ERROR","WARN","NOTICE","INFO","DEBUG","TRACE","ALL");

    function __construct($name,$level=LEVEL_ALL) {
        $this->basename=$name;
        $this->setLevel($level);
    }

    function setLevel($level) {
        if (is_numeric($level) ) {$this->level=intval($level); return; }
        foreach (Logger::$levels as $idx => $lvl) {
            if ( strtoupper($level)===$lvl) { $this->level=$idx; return; }
        }
    }

    function getLevel() {
        return $this->level;
    }

    function log($level,$msg) {
        if ($level>$this->level) return "";
        $trace=debug_backtrace();
        $str=Logger::$levels[$level]." ".$trace[2]['file']."::".$trace[2]['line']."::".$trace[2]['function']."() : ".$msg;
        error_log($str);
        return $str;
    }

    function trace($msg) { return $this->log(LEVEL_TRACE,$msg); }
    function debug($msg) { return $this->log(LEVEL_DEBUG,$msg); }
    function info($msg)  { return $this->log(LEVEL_INFO,$msg); }
    function notice($msg) { return $this->log(LEVEL_NOTICE,$msg); }
    function warn($msg) { return $this->log(LEVEL_WARN,$msg); }
    function error($msg) { return $this->log(LEVEL_ERROR,$msg); }
    function alert($msg) { return $this->log(LEVEL_ALERT,$msg); }
    function panic($msg) { die ($this->log(LEVEL_PANIC,$msg)); }

    function enter($data=null) {
        $msg=($data===null)?"()":'( '.json_encode($data).' )';
        return ($this->log(LEVEL_TRACE,"Enter{$msg}"));
    }
    function leave($data=null) {
        $msg=($data===null)?"()":'( '.json_encode($data).' )';
        return ($this->log(LEVEL_TRACE,"Leave{$msg}"));
    }

    function query($msg) {
        if ($this->level<=LEVEL_INFO) return "";
        $trace=debug_backtrace();
        $tr=$trace[1];
        if (array_key_exists(2,$trace)) $tr=$trace[2];
        $str="QUERY ".$this->basename."::".$tr['function']."() :\n".$msg;
        error_log($str);
        return $str;
    }
} // class logger

/*
DBConnection.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * DB connection handler
 * This class should only be used from DBConnection objects
 * @author jantonio
 *
 */
class DBConnection {

    private static $connections=array();

    /*
     * Check for running mysql daemon
     * @return string
     */
    public static function isDatabaseRunning() {
        $osName = strtoupper(PHP_OS);
        /* buscamos la lista de procesos */
        switch ($osName) {
            case 'WINDOWS':
            case 'WIN32':
            case 'WINNT':
                // la linea buscada empezara por mysqld.exe
                $exec_string = 'tasklist /FO TABLE /NH 2>NUL';
                break;
            case 'LINUX':
            case 'DARWIN':
                // en ubuntu el servicio es "mysqld"; en fedora "mariadb"
                // por eso, en lugar de buscar el programa, tenemos en cuenta que
                // la linea buscada empieza por mysql que es el nombre del usuario
                $exec_string = 'ps -aux';
                break;
            default: return "Unknown operating system";
        }
        $task_list = array();
        $return=0;
        exec($exec_string, $task_list,$return);
        $p1='/^mysql(d.exe)*/';
        $p2='/mariadbd/';
        $p3='/mysqld/';
        foreach ($task_list as $task_line)        {
            // echo "{$task_line}<br/>";
            if (preg_match($p1, $task_line, $out)) return "";
            if (preg_match($p2, $task_line, $out)) return "";
            if (preg_match($p3, $task_line, $out)) return "";
        }
        return "Database server is not running";
    }

    /**
     * Singleton static method to create database connection
     * @param $host
     * @param $name
     * @param $user
     * @param $pass
     * @return null
     */
    public static function getConnection($host,$name,$user,$pass) {
        $key="$host:$name:$user";
        if (!array_key_exists($key,self::$connections)) {
            $conn = @new mysqli($host,$user,$pass,$name);
            if ($conn->connect_error) return null;
            // not recommended in manual as doesn't properly handle real_escape_string
            // $conn->query("SET NAMES 'utf8'");
            $conn->set_charset("utf8");
            self::$connections[$key]=array($conn,0);
        }
        self::$connections[$key][1]++; // increase link count
        return self::$connections[$key][0];
    }

    public static function getRootConnection() {
        $myConfig=Config::getInstance();
        $h=$myConfig->getEnv("database_host");
        $n=$myConfig->getEnv("database_name");
        $u=base64_decode($myConfig->getEnv("database_ruser"));
        $p=base64_decode($myConfig->getEnv("database_rpass"));
        return self::getConnection($h,$n,$u,$p);
    }

    public static function closeConnection($conn) {
        foreach(self::$connections as $key => $val) {
            if ($val[0]!==$conn) continue;
            $val[1]--;
            if ($val[1]>0) return;
            $conn->close();
            unset (self::$connections[$key]);
            return;
        }
    }
} // class DBConnection

/*
DBObject.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
class DBObject {
    public $conn;
    protected $file;
    public $errormsg; // should be public to access to from caller
    protected $myLogger;
    protected $myConfig;

    private $fall; // boolean to notice use of fetch_all() or fetch_array loop
    /**
     * Constructor
     * @param {string} $file caller for this object
     * @throws Exception if cannot contact database
     */
    function __construct($file) {
        // connect database
        $this->file=$file;
        $this->myConfig=Config::getInstance();
        $h=$this->myConfig->getEnv("database_host");
        $n=$this->myConfig->getEnv("database_name");
        $u=base64_decode($this->myConfig->getEnv("database_user"));
        $p=base64_decode($this->myConfig->getEnv("database_pass"));
        $l=$this->myConfig->getEnv("debug_level");
        $this->myLogger= new Logger($file,$l);
        // $this->myLogger->trace("host:$h db:$n user:$u pass:$p");
        $this->conn=DBConnection::getConnection($h,$n,$u,$p);
        if (!$this->conn) {
            $this->errormsg="$file::construct() cannot contact database";
            throw new Exception($this->errormsg);
        }
        // check if exists resultset::fetch_all() method
        $this->fall= (method_exists('mysqli_result', 'fetch_all'))?true:false;
    }

    /**
     * Destructor
     * Just disconnect from database
     */
    function  __destruct() {
        // DBConnection::closeConnection($this->conn);
    }

    function error($msg) {
        $trace=debug_backtrace();
        $this->errormsg=$this->file."::".$trace[1]['function']."() Error at ".$trace[1]['file'].":".$trace[1]['line'].":\n".$msg;
        return null;
    }

    function query($sql) {
        $this->myLogger->query($sql);
        return $this->conn->query($sql);
    }

    function fetch_all($rs) {
        // estilo mysqlnd
        if ($this->fall) return $rs->fetch_all(MYSQLI_ASSOC);
        // estilo mysqli
        $res= array();
        while ($row= $rs->fetch_array(MYSQLI_ASSOC)) array_push($res,$row);
        return $res;
    }

    /**
     * if running in master server, set ServerID as ID when required
     * @param {string} $table to insert into
     * @param {integer} $id ID of affected row
     */
    function fixServerID($table){
        // if not in master server do nothing
        if (!inMasterServer($this->myConfig)) return "";
        $where="";
        if ($table=="perros") $where=" AND ( (Licencia IS NULL) OR (Licencia='') ";
        $sql="UPDATE {$table} SET ServerID=ID WHERE (ServerID=0) {$where}";
        $rs=$this->query($sql);
        if (!$rs) return $this->error($this->conn->error);
        return "";
    }

    /**
     * if running in master server, set ServerID as ID on last insert
     * @param {string} $table to insert into
     * @param {integer} $id ID of affected row
     */
    function setServerID($table,$id) {
        // if not in master server do nothing
        if (!inMasterServer($this->myConfig)) return "";
        // on server, every insert in Jueces,Clubes, Perros and Guias
        // should set their server id to be same as their ID
        $sql="UPDATE {$table} SET ServerID={$id} WHERE (ID={$id})";
        $rs=$this->query($sql);
        if (!$rs) return $this->error($this->conn->error);
        return "";
    }

    /**
     * Generic function to handle delete from child classes
     * @param string $from table to delete from
     * @param string $where where clause
     */
    function __delete($from,$where="") {
        $str="DELETE FROM {$from} WHERE {$where}";
        if ($where==="") $str="DELETE FROM {$from}";
        $res=$this->query($str);
        return $res;
    }

    /**
     * Generic function for handle select() on child classes
     * @param string $sel SELECT clause (required)
     * @param string $from FROM clause (required)
     * @param string $where WHERE clause (optional)
     * @param string $order ORDER BY clause (optional)
     * @param string $limit LIMIT offset,rows clause (optional)
     * @param string $group GROUP BY clause (optional)
     * @return {array} result (total,rows)
     */
    function __select($select,$from,$where,$order="",$limit="",$group="") {
        // if $limit is not null, perform a first count query
        $result=array();
        if ($where!=="") $where=" WHERE ".$where;
        if ($group!=="") $group=" GROUP BY ".$group;
        if ($order!=="") $order=" ORDER BY ".$order;
        if ($limit!=="") $limit=" LIMIT ".$limit;
        $result["total"]=0;
        if ($limit!=="") {
            $str= "SELECT count(*) FROM $from $where $group";
            $rs=$this->query($str);
            if (!$rs) return $this->error($this->conn->error);
            $row=$rs->fetch_array();
            $result["total"] = $row[0];
            $rs->free();
            // if (rowcount==0) no need to perform a second query
            if ($result["total"]==0) {
                $result["rows"]=array();
                return $result;
            }
        }
        // compose real request
        $str="SELECT $select FROM $from $where $group $order $limit";
        // make query
        $rs=$this->query($str);
        if (!$rs) return $this->error($this->conn->error); // returns null
        // generate result
        $result["rows"] = $this->fetch_all($rs);
        if ($result["total"]==0) $result["total"] = $rs->num_rows;
        $rs->free();
        return $result;
    }

    // mysql has "affected_rows" variable, but sometimes need "matched_rows"
    // to take care on update success, but no real change in row
    // from: https://stackoverflow.com/questions/5289475/get-number-of-rows-matched-by-update-query-with-php-mysqli
    function matched_rows() {
        preg_match_all('!\d+!', $this->conn->info, $m);
        return $m[0][0];
    }

    /**
     * Perform a query that returns first (should be the unique) element
     * as an Object
     * @param {string} $select SELECT clause (required)
     * @param {string} $from FROM clause (required)
     * @param {string} $where WHERE clause
     * @return {object|string} object on success; else error string
     */
    function __selectObject($select,$from,$where) {
        // compose SQL query
        $str="SELECT $select FROM $from";
        if ($where!=="") $str= $str." WHERE ".$where;
        // make query
        $rs=$this->query($str);
        if (!$rs) return $this->error($this->conn->error); // returns null
        // generate result
        $result=$rs->fetch_object();
        $rs->free();
        return $result;
    }

    /**
     * Perform a query that returns first (and unique) element
     * as an associative array
     * @param {string} $select SELECT clause (required)
     * @param {string} $from FROM clause (required)
     * @param {string} $where WHERE clause
     * @return {array|string} array on success; else error string
     */
    function __selectAsArray($select,$from,$where="") {
        $obj=$this->__selectObject($select,$from,$where);
        if (!is_object($obj)) return $obj;
        return json_decode(json_encode($obj), true);
    }

    /**
     * Retrieves objects from database by given (table,id) pair
     * @param {string} $table where to search object from
     * @param {integer} $id primary key of requested object
     * @return {object|string} obj if found, else error string
     */
    function __getObject($table,$id) { return $this->__selectObject("*",$table,"(ID=$id)"); }
    function __getArray($table,$id) { return $this->__selectAsArray("*",$table,"(ID=$id)"); }
} // class DBObject


/*
Eventos.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Eventos extends DBObject {

    protected $sessionID; // ring

    /**
     * Constructor
     * @param {string} $file caller for this object
     * @param {integer} $ring number
     * @throws Exception if cannot contact database or invalid Session ID
     */
    function __construct($file,$ring) {
        parent::__construct($file);
        if ( $ring<=0 ) {
            $this->errormsg="$file::construct() invalid ring number {$ring}";
            throw new Exception($this->errormsg);
        }
        // extract sessionID from ring number
        $res=$this->__selectObject("ID","Sesiones","Nombre LIKE '%Ring ".$ring."%'");
        $this->myLogger->info("Session ID for ring {$ring} is {$res->ID}");
        $this->sessionID=$res->ID;
    }

    /**
     * Send event to server as json data. Ignore response
     * and receive answer
     * @param {string} $host
     * @param {int} $port
     * @throws Exception
     */
    function uploadEvent($data) {
        // first of all, check internet conectivity
        if (isNetworkAlive()<0) {
            throw new Exception("event_sender::uploadEvent(): No internet access available");
        }
        $url=$this->myConfig->getEnv("remote_events_url");
        // do not verify cert on localhost
        $checkcert=true;
        if (strpos($url,"localhost")!=false)  $checkcert=false;
        if (strpos($url,"127.0.0.1")!=false)  $checkcert=false;
        $hdata=array(
            "Operation" => 'putEvent'
        );
        $pdata=array(
            'Data' => $data
        );
        // prepare and execute json request
        $curl = curl_init($url."?".http_build_query($hdata) );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow server redirection
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
        curl_setopt($curl, CURLOPT_POSTREDIR, 1); // do not change from post to get on "301 redirect"
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pdata );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $checkcert); // set to false when using "localhost" url
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 5); // wait 5 secs to attemp connect

        $json_response = @curl_exec($curl); // supress stdout warning
        if ( curl_error($curl) ) {
            $this->myLogger->error("event::uploadEvent() call to URL $url failed: " . curl_error($curl) );
        }
        // close curl stream
        curl_close($curl);
    }

    /**
     * As getEvents() but don't wait for new events, just list existing ones
     * @param {integer} SessionID (1+ring)
     * @param {integer} lastID last eventID
     * @param {string} type (optional) event type
     * @return array|null {array} available events for session $data['Session'] with id greater than $data['ID']
     * available events for session $data['Session'] with id greater than $data['ID']
     */
    function getEvents($lastID,$type="") {
        // $this->myLogger->enter();
        // sessionID == 1 means allow events from _any_ source
        $ses="";
        if ($this->sessionID>1) $ses="( Session = {$this->sessionID} ) AND";
        // check for search specific event type
        $extra="";  if ($type!=="") $extra=" AND ( Type = '".$type."' )";
        // perform query
        $result=$this->__select(
        /* SELECT */ "*",
            /* FROM */ "eventos",
            /* WHERE */ "$ses ( ID > {$lastID} ) $extra",
            /* ORDER BY */ "ID",
            /* LIMIT */ ""
        );
        //$this->myLogger->leave();
        return $result;
    }

    /**
     * Retrieve last "init" event with provided Session ID
     * Used for clients to retrieve event ID index
     * SELECT * from Eventos
     *		WHERE  ( Session = {$data['Session']} ) AND ( Type = 'init' )
     *		ORDER BY ID DESC LIMIT 1
     * @param {array} $data key:value pairs to extract parameters from
     * @param {array} $data requested event info
     * @return {array} data about last "open" event with provided session id
     */
    function connect() {
        // $this->myLogger->enter();
        // buscamos el ultimo evento 'init' de la sesion solicitada
        // o bien si sessionID es 1, de cualquier sesion
        if (intval($this->sessionID)>1) $str=" AND ( Session = {$this->sessionID} )";
        $result=$this->__select(
        /* SELECT */ "*",
            /* FROM */ "eventos",
            /* WHERE */ "( Type = 'init' ) $str",
            /* ORDER BY */ "ID DESC",
            /* LIMIT */ "0,1"
        );
        // $this->myLogger->leave();
        if ($result['total']!=0) { // send connection message to console
            // compose message to be shown on console as timeout:message
            $message="Connect From event_sender Ring:{$this->sessionID} Dest:{$this->myConfig->getEnv("remote_event_url")}";
        }
        return $result;
    }
} // Class Eventos

date_default_timezone_set("Europe/Madrid");
// also send error to display
ini_set('display_errors', 'On');
ini_set('html_errors', 0);
// track erros and write to own log file
ini_set("log_errors","On");
ini_set("error_log",__DIR__."/event_sender.log"); //store in local directory
// on MAC-OSX tmpdir is set to login user tmp directory.
// As xampp runs as user 'daemon', write permission to this 'tmp' directory fails.
// so make sure to set a valid tmp directory for everyone. A bit risky, but works
if (strtoupper(PHP_OS)==="DARWIN") putenv('TMPDIR=/tmp');

$ring=(array_key_exists(1,$argv))?intval($argv[1]):0;
$url=(array_key_exists(2,$argv))?$argv[2]:"";
if ($ring<=0) {
    echo "Must provide a valid ring number".PHP_EOL;
    die("Usage: {$argv[0]} ring url".PHP_EOL);
}
if (filter_var($url, FILTER_VALIDATE_URL) === FALSE)  {
    echo "Argument {$url} is not a valid URL".PHP_EOL;
    die("Usage: {$argv[0]} ring url".PHP_EOL);
}

// get and set configuration
$c=Config::getInstance();
$c->setEnv("remote_events_url",$url);
// get and set logger
$l=new Logger("event_sender");
$l->setLevel($c->getEnv('debug_level'));

// create event parser object
$e=new Eventos("event_sender",$ring);

// loop until connect
$lastEventID=0;
$l->trace("Connecting to AgilityContest server");
while($lastEventID==0) {
    $res=$e->connect();
    if ($res['total']==0) {
        $l->info("No 'init' event yet on ring {$ring}. Retrying in 5 seconds");
        sleep(5);
        continue;
    }
    $lastEventID=$res['rows'][0]['ID'];
}
$l->trace("Init event ID for ring {$ring} is {$lastEventID}");
// loop for incomming events
while (true) {
    $evts=$e->getEvents($lastEventID);
    if ($evts['total']==0) { sleep(5); continue; }
    foreach ($evts['rows'] as $event) {
        $l->trace("sending event: ".json_encode($event));
        $e->uploadEvent($event['Data']);
        $lastEventID=$event['ID'];
    }
    sleep(1);
}



?>

