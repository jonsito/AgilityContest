<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
require_once(__DIR__."/server/tools.php");
require_once(__DIR__."/server/auth/Config.php");
$config =Config::getInstance();

// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/server/upgradeVersion.php");
 
/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
	die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}

if (strtoupper(substr(PHP_OS, 0, 3)) !== 'LIN') {
	// en windows/android hay que usar https para que las cosas funcionen
	if (!is_https()) die("You MUST use https protocol to access this application");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/locale/easyui-lang-<?php echo substr($config->getEnv('lang'),0,2);?>.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-groupview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/clubes.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/guias.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/perros.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/jueces.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/usuarios.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/sesiones.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/tandas.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/equipos.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/pruebas.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/inscripciones.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript">
function initialize() {
    var mm=$('#mymenu');
	// expand/collapse menu on mouse enter/exit
	setHeader("");
	mm.mouseenter(function(){$('#mymenu').panel('expand');});
	mm.mouseleave(function(){$('#mymenu').panel('collapse');});
	
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(authInfo.SessionKey)!=='undefined' && authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',authInfo.SessionKey);
		}
	    return true;
	  }
	});
	
	// load login page
	loadContents("/agility/client/frm_login.php","");
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @paramm {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>'; // even rows
	var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>'; // odd rows
	var c3='<?php echo $config->getEnv('easyui_rowcolor3'); ?>'; // extra color for special rows
	if (idx<0) return res+c3+";";
    if ((idx & 0x01) == 0) {
        return res + c1 + ";";
    } else {
        return res + c2 + ";";
    }
}

var ac_config= {
		// version, logging y depuracion
		'debug_level'		: '<?php echo $config->getEnv('debug_level'); ?>',
		'version_name'		: '<?php echo $config->getEnv('version_name'); ?>',
		'version_date'		: '<?php echo $config->getEnv('version_date'); ?>',

		// Internacionalizacion. Idiomas
		'lang'				: '<?php echo $config->getEnv('lang'); ?>',
		// variables del sistema
		'proximity_alert'	: <?php echo $config->getEnv('proximity_alert'); ?>,
		
		// entorno grafico
		'easyui_theme' 		: '<?php echo $config->getEnv('easyui_theme'); ?>',
		'easyui_bgcolor'	: '<?php echo $config->getEnv('easyui_bgcolor'); ?>',
		'easyui_hdrcolor'	: '<?php echo $config->getEnv('easyui_hdrcolor'); ?>',
		'easyui_opcolor'	: '<?php echo $config->getEnv('easyui_opcolor'); ?>',
		'easyui_rowcolor1'	: '<?php echo $config->getEnv('easyui_rowcolor1'); ?>',
		'easyui_rowcolor2'	: '<?php echo $config->getEnv('easyui_rowcolor2'); ?>',
		// configuracion del videowall
		'vw_polltime'		: <?php echo $config->getEnv('vw_polltime'); ?>,
		'vw_alpha'			: <?php echo $config->getEnv('vw_alpha'); ?>,
		'vw_hdrfg1'			: '<?php echo $config->getEnv('vw_hdrfg1'); ?>',
		'vw_hdrbg1'			: '<?php echo $config->getEnv('vw_hdrbg1'); ?>',
		'vw_hdrfg2'			: '<?php echo $config->getEnv('vw_hdrfg2'); ?>',
		'vw_hdrbg2'			: '<?php echo $config->getEnv('vw_hdrbg2'); ?>',
		'vw_rowcolor1'		: '<?php echo $config->getEnv('vw_rowcolor1'); ?>',
		'vw_rowcolor2'		: '<?php echo $config->getEnv('vw_rowcolor2'); ?>',
		// generacion de PDF's
		'pdf_topmargin'		: '<?php echo $config->getEnv('pdf_topmargin'); ?>',
		'pdf_leftmargin'	: '<?php echo $config->getEnv('pdf_leftmargin'); ?>',
		'pdf_hdrfg1'		: '<?php echo $config->getEnv('pdf_hdrfg1'); ?>',
		'pdf_hdrbg1'		: '<?php echo $config->getEnv('pdf_hdrbg1'); ?>',
		'pdf_hdrfg2'		: '<?php echo $config->getEnv('pdf_hdrfg2'); ?>',
		'pdf_hdrbg2'		: '<?php echo $config->getEnv('pdf_hdrbg2'); ?>',
		'pdf_rowcolor1'		: '<?php echo $config->getEnv('pdf_rowcolor1'); ?>',
		'pdf_rowcolor2'		: '<?php echo $config->getEnv('pdf_rowcolor2'); ?>',
		'pdf_linecolor'		: '<?php echo $config->getEnv('pdf_linecolor'); ?>',
		
		// personalizacion del tablet
		// personalizacion del tablet
		'tablet_beep'		: <?php echo toBoolean($config->getEnv('tablet_beep'))?'true':'false'; ?>,
		'tablet_dnd'		: <?php echo toBoolean($config->getEnv('tablet_dnd'))?'true':'false'; ?>,
		'tablet_chrono'		: <?php echo toBoolean($config->getEnv('tablet_chrono'))?'true':'false'; ?>,
		'tablet_countdown'	: <?php echo $config->getEnv('tablet_countdown'); ?>
}

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

<!-- CABECERA -->
<div id="myheader">
	<p> <a href="/agility/index.php">Agility Contest</a> </p>
	<span id="Header_Operation"></span>
</div>

<!-- LOGO -->
<div id="mylogo">
	<p><img id="logo_AgilityContest" src="/agility/images/AgilityContest.png" alt="AgilityContest" width="200" height="160"/></p>
	<p><img id="logo_Federation" src="/agility/images/logos/rsce.png" alt="Federation" width="200" height="160"/></p>
</div>

<!-- MENU LATERAL -->
<div id="mysidebar">

<div id="mymenu" class="easyui-panel" title="Men&uacute; de Operaciones"
	data-options="border:true,closable:false,collapsible:true,collapsed:true">
<ul>
<li>
	<ul>
	<li><a id="menu-Login" href="javascript:showLoginWindow();">
		<span id="login_menu-text">Iniciar sesi&oacute;n</span></a>
	</li>
	</ul>
</li>
<li>BASE DE DATOS
	<ul>
	<li><a href="javascript:loadContents('/agility/client/frm_clubes.php','Gesti&oacute;n de la Base de Datos de Clubes');">Clubes</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_guias.php','Gesti&oacute;n de la Base de Datos de Gu&iacute;as');">Gu&iacute;as</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_perros.php','Gesti&oacute;n de la base de datos de Perros');">Perros</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_jueces.php','Gesti&oacute;n de la Base de datos de Jueces');">Jueces</a></li>
	</ul>
</li>
<li>PRUEBAS
	<ul>
	<li><a href="javascript:loadContents('/agility/client/frm_pruebas.php','Creaci&oacute;n y edici&oacute;n de pruebas');">Creaci&oacute;n de pruebas</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_inscripciones.php','Inscripciones - Selecci&oacute;n de prueba',{'s':'#selprueba-window'});">Edici&oacute;n. Inscripciones</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_competicion.php','Competicion - Selecci&oacute;n de Prueba y Jornada');">Desarrollo de la prueba</a></li>
	</ul>
</li>
<li>CONSULTAS
	<ul>
	<li><a href="javascript:loadContents('/agility/client/frm_clasificaciones.php','Clasificaciones - Selecci&oacute;n de Prueba y Jornada');">Clasificaciones</a></li>
	<li><a href="javascript:loadContents('/agility/client/frm_estadisticas.php','Estad&iacute;sticas');">Estad&iacute;sticas</a></li>
	</ul>
</li>
<li>HERRAMIENTAS
	<ul>
	<li> <a href="javascript:loadContents('/agility/client/frm_admin.php','Administraci&oacute;n')">Administraci&oacute;n</a></li>
	<li><a id="menu-Login" href="javascript:showMyAdminWindow();">Acceso a BBDD</a></li>
	</ul>
</li>
<li>DOCUMENTACION
	<ul>
	<li> <a target="documentacion" href="/agility/client/manual.html">Manual en l&iacute;nea</a></li>
	<li> <a href="javascript:loadContents('/agility/client/frm_registration.php','Informaci&oacute;n de registro')">Datos de registro</a></li>
	<li> <a href="javascript:loadContents('/agility/client/frm_about.php','Sobre la aplicaci&oacute;n...')">Acerca de...</a></li>
	</ul>
</li>
</ul>
</div> <!-- mymenu -->
</div> <!-- mysidebar -->
	
<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->
<div id="mycontent">
	<div id="contenido" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false,"></div>
</div>

</body>

</html> 
