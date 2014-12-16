<?php 
require_once(__DIR__."/server/auth/Config.php");
$config =new Config()
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
<title>Agility Contest</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/locale/easyui-lang-es.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/clubes.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/guias.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/perros.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/jueces.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/usuarios.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/sesiones.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/equipos.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/pruebas.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/inscripciones.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript">
function initialize() {
	// expand/collapse menu on mouse enter/exit
	setHeader("");
	$('#mymenu').mouseenter(function(){$('#mymenu').panel('expand');})
	$('#mymenu').mouseleave(function(){$('#mymenu').panel('collapse');})
	
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR, settings) {
		if (!workingData.sessionKey) return;
		jqXHR.setRequestHeader('X-AC-SessionKey',workingData.sessionKey);
	    return true;
	  }
	});
	
	// load a default page
	loadContents("/agility/client/frm_main.php","");
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @paramm {integer} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
	var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";" } else { return res+c2+";" }
}
 
</script>
<style>
/* Common CSS tags for Agility Contest */

body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }

/***** Datos de la cabecera ******/
#mylogo { position: fixed; top: 0px; right: 10px; }
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
	<p><img src="/agility/images/AgilityContest.png" alt="AgilityContest" width="200" height="160"/></p>
</div>

<!-- MENU LATERAL -->
<div id="mysidebar">

<div id="mymenu" class="easyui-panel" title="Men&uacute; de Operaciones"
	data-options="border:true,closable:false,collapsible:true,collapsed:true">
<ul>
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
	<li><a href="javascript:loadContents('/agility/client/frm_inscripciones.php','Inscripciones - Selecci&oacute;n de prueba');">Edici&oacute;n. Inscripciones</a></li>
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
	<li> <a target="phpMyAdmin" href="/phpmyadmin">phpMyAdmin</a></li>
	</ul>
</li>
<li>DOCUMENTACION
	<ul>
	<li> <a target="documentacion" href="/agility/client/manual.html">Manual en l&iacute;nea</a></li>
	<li> <a href="javascript:loadContents('/agility/client/frm_about.php','Sobre la aplicaci&oacute;n...')">Acerca de...</a></li>
	</ul>
</ul>
</div> <!-- mymenu -->
</div> <!-- mysidebar -->
	
<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->
<div id="mycontent">
	<div id="contenido" class="easyui-panel" style="background:none" data-options="width:'100%',fit:true,border:false,"></div>
</div>

</body>

</html> 
