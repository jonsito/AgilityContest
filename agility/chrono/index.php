<?php
/*
 chrono/index.php

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
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
if ( intval($config->getEnv('restricted'))!=0) {
    die("Access other than public directory is not allowed");
}
// tool to perform automatic upgrades in database when needed
require_once(__DIR__."/../server/upgradeVersion.php");
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
<title>AgilityContest (Chrono)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/chrono_css.php" />
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-groupview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fittext-1.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/chrono/chrono.js" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript" charset="utf-8">
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(authInfo.SessionKey)!=undefined && authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',authInfo.SessionKey);
		}
	    return true;
	  }
	});
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
    'register_events'	: <?php echo $config->getEnv('register_events'); ?>,
    'reset_events'	    : <?php echo $config->getEnv('reset_events'); ?>,

	// entorno grafico
	'easyui_theme' 		: '<?php echo $config->getEnv('easyui_theme'); ?>',
	'easyui_bgcolor'	: '<?php echo $config->getEnv('easyui_bgcolor'); ?>',
	'easyui_hdrcolor'	: '<?php echo $config->getEnv('easyui_hdrcolor'); ?>',
	'easyui_opcolor'	: '<?php echo $config->getEnv('easyui_opcolor'); ?>',
	'easyui_rowcolor1'	: '<?php echo $config->getEnv('easyui_rowcolor1'); ?>',
	'easyui_rowcolor2'	: '<?php echo $config->getEnv('easyui_rowcolor2'); ?>',
	'easyui_rowcolor3'	: '<?php echo $config->getEnv('easyui_rowcolor3'); ?>',
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
	'tablet_beep'		: <?php echo toBoolean($config->getEnv('tablet_beep'))?'true':'false'; ?>,
	'tablet_dnd'		: <?php echo toBoolean($config->getEnv('tablet_dnd'))?'true':'false'; ?>,
	'tablet_chrono'		: <?php echo toBoolean($config->getEnv('tablet_chrono'))?'true':'false'; ?>,
	'tablet_next'		: <?php echo toBoolean($config->getEnv('tablet_next'))?'true':'false'; ?>,
	'tablet_countdown'	: <?php echo $config->getEnv('tablet_countdown'); ?>
}
</script>
<style>
body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }
</style>
</head>

<body style="margin:0;padding:0;background-color:blue;font-size:100%">
<div id="chrono-contenido" style="width:inherit;height:inherit;margin:0;padding:0">

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="chrono-dialog" class="easyui-dialog" style="position:relative;width:350px;height:auto;padding:10px 10px">
	<form id="chrono-Selection">
    	<div class="fitem">
       		<label for="Prueba">Ring:</label>
       		<select id="chrono-Session" name="Session" style="width:200px"></select>
    	</div>
	</form>
</div> <!-- Window -->

<div id="chrono-Buttons" style="text-align:right">
   	<a id="chrono-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls: 'icon-ok'" onclick="chrono_accept()">Aceptar</a>
</div>	<!-- botones -->

</div> <!-- contenido -->

<script type="text/javascript">
$('#chrono-dialog').dialog({
	title: 'Selecciona ring',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: true,
	modal: true,
	buttons: '#chrono-Buttons' 
});

$('#chrono-form').form();

addTooltip($('#chrono-okBtn').linkbutton(),"Trabajar con la sesi&oacute;n seleccionada");

$('#chrono-Session').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/sessionFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	rownumber: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false, // avoid keyboard deploy
	columns: [[
	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
		{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: 'Nombre' },
		{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: 'Observaciones' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='selectring';
		param.Hidden=0;
		return true;
	},
	onLoadSuccess: function(data) {
		$('#chrono-Session').combogrid('setValue',2); // by default select session 2 (ring 1)
	}
});

function chrono_accept() {
	// si prueba invalida cancelamos operacion
	var s=$('#chrono-Session').combogrid('grid').datagrid('getSelected');
	if ( s===null ) {
		// indica error
		$.messager.alert("Error","Debe indicar una sesion v&aacute;lidas","error");
		return;
	}
	// clear selection to make sure next time gets empty
	$('#chrono-Session').combogrid('setValue','');
	
	// store selected data into global structure
	workingData.sesion=s.ID;
	workingData.nombreSesion=s.Nombre;
	initWorkingData(s.ID);
	var page='/agility/chrono/chrono.inc.php';
	$('#chrono-dialog').dialog('close');
	$('#chrono-contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#chrono-contenido').load('/agility/console/frm_notavailable.php');
			}
		);
}

</script>
</body>
</html> 
