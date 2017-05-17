<?php
ini_set('zlib.output_compression', 0);
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
if (!isset($config) ) $config=Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
	die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}
/* Check operating system against requested protocol */
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'LIN') {
	// en windows/android hay que usar https para que las cosas funcionen
	if (!is_https()) die("You MUST use https protocol to access this application");
}
/* check for properly installed xampp */
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
if ( intval($config->getEnv('restricted'))!=0) {
    die("Access other than public directory is not allowed");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<meta name="application-name" content="Agility Contest" />
<meta name="copyright" content="© 2013-2015 Juan Antonio Martinez" />
<meta name="author" lang="en" content="Juan Antonio Martinez" />
<meta name="description"
        content="A web client-server (xampp) app to organize, register and show results for FCI Dog Agility Contests" />
<meta name="distribution" 
	content="This program is free software; you can redistribute it and/or modify it under the terms of the 
		GNU General Public License as published by the Free Software Foundation; either version 2 of the License, 
		or (at your option) any later version." />
<title>AgilityContest (Console)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/public_css.php" />
<link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
<script src="/agility/lib/jquery-1.12.3.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/locale/easyui-lang-<?php echo substr($config->getEnv('lang'),0,2);?>.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/nicEdit/nicEdit.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/admin.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/console/import/import.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/clubes.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/guias.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/perros.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/jueces.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/usuarios.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/sesiones.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/modules.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/entrenamientos.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/tandas.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/equipos.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/pruebas.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/inscripciones.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/results_and_scores.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/printer.js.php" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript">

var ac_clientOpts = {
    'BaseName':'console',
    'Ring':1, // sessid:1 --> console (broadcast ring)
    'View':0,
    'Mode':0,
    'Timeout':0,
    'SessionName':''
};

function initialize() {
    var mm=$('#mymenu');
	// expand/collapse menu on mouse enter/exit
	setHeader("");
	mm.mouseenter(function(){$('#mymenu').panel('expand');});
	mm.mouseleave(function(){$('#mymenu').panel('collapse');});
	
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!==null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',ac_authInfo.SessionKey);
		}
	    return true;
	  }
	});
	// load configuration
	loadConfiguration();
	// get License Information
	getLicenseInfo();
	// retrieve info on available federation modules
	getFederationInfo();
	// initialize session data
	initAuthInfo();
	// load login page
	loadContents("/agility/console/frm_login.php","");
	var upgdiv=$('#upgradeVersion');
	if (typeof (ac_installdb) !== "undefined") {
        upgdiv.css('display','none'); // hide install log
    } else {
        upgdiv.scrollTop = upgdiv[0].scrollHeight;
    }
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function consoleRowStyler(idx,row) {
	// console.log("rwostyler row "+idx);
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>'; // even rows
	var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>'; // odd rows
	var c3='<?php echo $config->getEnv('easyui_rowcolor3'); ?>'; // extra color for special rows
	if (idx<0) return res+c3+";";
    if ((idx & 0x01) === 0) {
        return res + c1 + ";";
    } else {
        return res + c2 + ";";
    }
}

/**
 * rowStyler function for livestream secondary datagrids
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function consoleRowStyler2(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor3'); ?>';
	var c2='<?php echo $config->getEnv('easyui_rowcolor4'); ?>';
	if ( (idx&0x01)===0) { return res+c1+";"; } else { return res+c2+";"; }
}

function myRowStyler(idx,row) { return consoleRowStyler(idx,row); }
function myRowStyler2(idx,row) { return consoleRowStyler2(idx,row); }

/**
 * Generic event handler for console screens
 * Console has a 'eventHandler' table with pointer to functions to be called
 * @param id {number} Event ID
 * @param e {object} Event data
 */
function console_eventManager(id,e) {
    var evt=parseEvent(e); // remember that event was coded in DB as an string
    var accept=false;
    // si el evento es para la consola ( session = 1 ) se acepta
    if (evt['Session']==="1") accept=true;
    // si no es para la consola, pero es "init" o "reconfig" se acepta
    if (evt['Type']==='init') accept=true;
    if (evt['Type']==='reconfig') accept=true;
    // else se rechaza
    if (!accept) return;
    if (typeof(eventHandler[evt['Type']])==="function") {
        evt['ID']=id; // fix real id on stored eventData
        eventHandler[evt['Type']](evt); // and call specific event manager routine
    }
}

// alert login events on console
function console_noticeLogin(evt) {
    var data=evt['Value'].split(':');
    var str="<?php _e('Session init');?>:<br/>"+
        "<?php _e('User');?>: "+data[0] + " <br/>"+
        "<?php _e('On');?>: "  +data[1] + " <?php _e('Session ID');?>: "+data[2]+"<br/>"+
        "<?php _e('From');?>: "+ replaceAll(';',':',data[3]);
    $.messager.show({
        width: 300,
        height: 125,
        timeout: 2500,
        title: '<?php _e('Notice'); ?>',
        msg: str
    })
}

// handle showMessage event command
function console_showMessage(evt) {
    // value is in form timeout:text
    var data=event['Value'].split(':',2);
    if (data[1].length===0) return; // nothing to show :-)
    var tout=parseInt(data[0]);
    tout= (tout<=0)?1:tout;
    tout= (tout>=30)?30:tout;
    // and send message to console
    $.messager.show({
        width: 300,
        height: 75,
        timeout: 1000*tout,
        title: '<?php _e('Console'); ?>',
        msg: data
    })
}

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event){ // PENDING: notify to console every "init" event
        console_noticeLogin(event);
    },
    'open': null,// operator select tanda
    'close': null,    // no more dogs in tanda
    'datos': null,      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
    'llamada': null,    // llamada a pista
    'salida': null,     // orden de salida
    'start': null,      // start crono manual
    'stop': null,       // stop crono manual
    // nada que hacer aqui: el crono automatico se procesa en el tablet
    'crono_start':  null, // arranque crono automatico
    'crono_restart': null,// paso de tiempo intermedio a manual
    'crono_int':  	null, // tiempo intermedio crono electronico
    'crono_stop':  null, // parada crono electronico
    'crono_reset':  null, // puesta a cero del crono electronico
    'crono_error':  null, // fallo en los sensores de paso
    'crono_dat':    null, // datos desde crono electronico
    'crono_ready':    null, // chrono ready and listening
    'aceptar':	null, // operador pulsa aceptar
    'cancelar': null, // operador pulsa cancelar
    'camera':	null, // change video source
    'command': function(event){ // videowall remote control
        handleCommandEvent(
            event,
            [
                /* EVTCMD_NULL:         */ function(e) {console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ null,
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_OSDSETALPHA:  */ null,
                /* EVTCMD_OSDSETDELAY:  */ null,
                /* EVTCMD_NOTUSED7:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) {console_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ null
            ]
        )
    },
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};
	
</script>
<style>
/* Common CSS tags for Agility Contest */

body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }

/***** Datos de la cabecera ******/
#mylogo { position: fixed; top: 0; right: 10px; }
#myheader {	position: fixed; top: 10px; left: 10px; }
#myheader p { 
	color: <?php echo $config->getEnv('easyui_hdrcolor'); ?>; 
	padding-left: 20px; 
	font-family: Arial, sans-serif;
    font-size: 28pt;
    font-style: italic;
    font-weight: bold;
    display: table-cell;
}
#myheader p a:link {  text-decoration:none; color:<?php echo $config->getEnv('easyui_hdrcolor'); ?>; }      /* unvisited link */
#myheader p a:visited { text-decoration:none; color:<?php echo $config->getEnv('easyui_hdrcolor'); ?>; }  /* visited link */
#myheader p a:hover { text-decoration:none; color:<?php echo $config->getEnv('easyui_hdrcolor'); ?>; }  /* mouse over link */
#myheader p a:active { text-decoration:none; color:<?php echo $config->getEnv('easyui_hdrcolor'); ?>; }  /* selected link */
#myheader span p { font-size:24pt; padding-left: 250px; color:<?php echo $config->getEnv('easyui_opcolor'); ?>; }
</style>

</head>

<body onload="initialize();">
<div id="upgradeVersion" style="color:#fff;display:block;">
	<h1>Installing database... please wait</h1>
    <p>
		    <?php
            // perform automatic upgrades in database when needed
            require_once(__DIR__ . "/../server/upgradeVersion.php");
            ?>
        <script type="text/javascript">
            var ac_installdb=true;
            history.replaceState('data to be passed', 'AgilityContest Console', '/agility/console');
        </script>
	</p>
</div>

<!-- CABECERA -->
<div id="myheader">
	<p> <a href="/agility/console/index.php">Agility Contest</a> </p>
	<span id="Header_Operation"></span>
</div>

<!-- LOGO -->
<div id="mylogo">
	<p><img id="logo_AgilityContest" src="/agility/images/AgilityContest.png" alt="AgilityContest" width="200" height="160"/></p>
	<p><img id="logo_Federation" src="/agility/images/logos/rsce.png" alt="Federation" width="200" height="160"/></p>
</div>

<!-- MENU LATERAL -->
<div id="mysidebar">

<div id="mymenu" class="easyui-panel" title="<?php _e('Operations Menu');?>"
	data-options="border:true,closable:false,collapsible:true,collapsed:true">
<ul>
<li>
	<ul>
	<li><a id="menu-Login" href="javascript:showLoginWindow();">
		<span id="login_menu-text"><?php _e('Init Session');?></span></a>
	</li>
	</ul>
</li>
<li><?php _e('DATABASE'); ?>
	<ul>
	<li><a href="javascript:check_softLevel(access_level.PERMS_OPERATOR,function(){
	        loadCountryOrClub();}
	    );"><span id="menu-clubes"><?php _e('Clubs'); ?></span>
        </a>
    </li>
	<li><a href="javascript:check_softLevel(access_level.PERMS_OPERATOR,function(){
	        loadContents('/agility/console/frm_guias.php','<?php _e('Handlers Database Management');?>');
	    });"><?php _e('Handlers'); ?>
        </a>
    </li>
	<li><a href="javascript:check_softLevel(access_level.PERMS_OPERATOR,function(){
	        loadContents('/agility/console/frm_perros.php','<?php _e('Dogs Database Management');?>',{'p':'#perros-dialog'});
	    });"><?php _e('Dogs'); ?>
        </a>
    </li>
	<li><a href="javascript:check_softLevel(access_level.PERMS_OPERATOR,function(){
	        loadContents('/agility/console/frm_jueces.php','<?php _e('Judges Database Management');?>');
	    });"><?php _e('Judges'); ?>
        </a>
    </li>
	</ul>
</li>
<li><?php _e('CONTESTS'); ?>
	<ul>
	<li><a href="javascript:check_softLevel(access_level.PERMS_OPERATOR,function(){
	        loadContents('/agility/console/frm_pruebas.php','<?php _e('Create and Edit Contests');?>');
	    });"><?php _e('Create Contests'); ?>
        </a>
    </li>
	<li><a href="javascript:loadContents('/agility/console/frm_inscripciones.php','<?php _e('Inscriptions - Contest selection');?>',{'s':'#selprueba-window'});"><?php _e('Handle Inscriptions'); ?></a></li>
	<li><a href="javascript:loadContents('/agility/console/frm_competicion.php','<?php _e('Competition - Contest and Journey selection');?>');"><?php _e('Running Contests'); ?></a></li>
	</ul>
</li>
<li><?php _e('REPORTS'); ?>
	<ul>
	<li><a href="javascript:loadContents('/agility/console/frm_clasificaciones.php','<?php _e('Scores - Contest and Journey selection');?>');"><?php _e('Scores'); ?></a></li>
	<li><a href="javascript:loadContents('/agility/console/frm_estadisticas.php','<?php _e('Statistics');?>');"><?php _e('Statistics'); ?></a></li>
	</ul>
</li>
<li><?php _e('TOOLS'); ?>
	<ul>
	<li><a href="javascript:loadContents('/agility/console/frm_admin.php','<?php _e('Configuration');?>',{e:'#remote-dialog'})"><?php _e('Configuration'); ?></a></li>
	<li><a href="javascript:showMyAdminWindow();"><?php _e('Direct DB Access'); ?></a></li>
	</ul>
</li>
<li><?php _e('DOCUMENTATION'); ?>
	<ul>
	<li> <a target="documentacion" href="/agility/console/manual.html"><?php _e('OnLine Manual'); ?></a></li>
	<li> <a href="javascript:loadContents('/agility/console/frm_registration.php','<?php _e('License information');?>')"><?php _e('License information'); ?></a></li>
	<li> <a href="javascript:loadContents('/agility/console/frm_about.php','<?php _e('About AgilityContest');?>...')"><?php _e('About'); ?>...</a></li>
	</ul>
</li>
</ul>
</div> <!-- mymenu -->
</div> <!-- mysidebar -->
	
<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->
<div id="mycontent">
	<div id="contenido" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
</div>

<!--
	Entrada para insertar dialogos de importacion de ficheros desde excel
	Debido a que se utilizan en varios frames, no se pueden cargar directamente desde loadcontents
	sino que hay que cargarlos "bajo" demanda
	Para depuracion usamos php_include()
	 -->
<div id="myimport">
	<div id="importflag" style="display:none"></div> <!-- "" (empty) or "ready" -->
	<div id="importclubes" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
	<div id="importhandlers" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
	<div id="importdogs" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
	<div id="importinscriptions" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
	<div id="importcontest" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false"></div>
</div> <!-- to be filled -->

</body>

</html> 
