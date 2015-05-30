<?php
/*
 videowall/index.php

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
$config =Config::getInstance();
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
<title>AgilityContest (VideoWall)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
    <script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-groupview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fittext-1.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/videowall/videowall.js" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript" charset="utf-8">
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(authInfo.SessionKey)!=='undefined' && authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',authInfo.SessionKey);
		}
	    return true;
	  }
	});
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
    var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

function myTransparentRowStyler(idx,row) {
    var res="background-color:";
    var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
    var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
    if ( (idx&0x01)==0) { return res+c1+";opacity:0.9"; }
    else { return res+c2+";opacity:0.9"; }
}
/* same as above, but tracks tanda and team information */
function myLlamadaRowStyler(idx,row) {
    var res="background-color:";
    var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
    var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
    var tnd='<?php echo $config->getEnv('vw_hdrbg2'); ?>';
    var eqp='<?php echo $config->getEnv('vw_hdrbg3'); ?>';
    if (parseInt(row.Orden)==-1) return res+tnd+";";
    if (parseInt(row.Orden)==0) return res+eqp+";";
    if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
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
    'reset_events'	: <?php echo $config->getEnv('reset_events'); ?>,

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
    'vw_hdrfg3'			: '<?php echo $config->getEnv('vw_hdrfg3'); ?>',
    'vw_hdrbg3'			: '<?php echo $config->getEnv('vw_hdrbg3'); ?>',
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

    body {
        /* default background from environment */
        font-size: 100%;
        background: <?php echo $config->getEnv('easyui_bgcolor'); ?>;
    }

    /* remove underlines around footer imagelinks */
    a,
    a img {
        text-decoration: none;
        outline: none;
        border: 0px none transparent;
    }

    .datagrid_vw-class {
        background:transparent;
        background-color: white;
        filter:alpha(opacity=60);
        -moz-opacity:0.6;
        opacity:0.6;
        border: 1px solid black;
    }

    /* ajuste de las cabeceras de los datagrid groupview */
    .datagrid-body .datagrid-group {
        background-color: <?php echo $config->getEnv('vw_hdrbg2'); ?>;
        height:40px;
        line-height: 40px;
    }
    .datagrid-body .datagrid-group .datagrid-group-title {
        height:40px;
        line-height: 40px;
        font-weight: bold;
    }
    .datagrid-body .datagrid-group .datagrid-group-expander {
        margin-top:7px;
    }

</style>

</head>

<body style="margin:0;padding:0;background-color:blue;font-size:100%">
<div id="vw_contenido" style="width:inherit;height:inherit;margin:0;padding:0">

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="selvw-dialog" class="easyui-dialog" style="position:relative;width:500px;height:220px;padding:20px 20px">
	<form id="selvw-Selection">
    	<div class="fitem">
       		<label for="Prueba">Selecciona Sesi&oacute;n:</label>
       		<select id="selvw-Session" name="Session" style="width:200px"></select>
    	</div>
    	<div class="fitem">
       		<label for="Vista">Selecciona Vista:</label>
       		<select id="selvw-Vista" name="Vista" style="width:200px">
				<option value="0">Orden de Salida</option>
				<option value="1">Llamada a pista</option>
				<option value="2">Resultados Provisionales</option>
                <option value="3">Live Stream OSD</option>
                <option value="5">Live Stream Parciales</option>
				<option value="4">Vista Combinada</option>
       		</select>
    	</div>
    	
	</form>
</div> <!-- Window -->

<div id="selvw-Buttons" style="text-align:right">
   	<a id="selvw-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls: 'icon-ok'" onclick="vw_accept()">Aceptar</a>
</div>	<!-- botones -->

</div> <!-- contenido -->

<script type="text/javascript">

//add 'callback' property to store interval references
$.extend($.fn.window.defaults,{callback:null});

$('#selvw-dialog').dialog({
	title: 'Datos de la Vista a desplegar',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: true,
	modal: true,
	buttons: '#selvw-Buttons' 
});

$('#selvw-form').form();

addTooltip($('#selvw-okBtn').linkbutton(),"Trabajar con la sesión seleccionada");

$('#selvw-Session').combogrid({
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
		{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: 'Observaciones' },
        { field:'Prueba',	    hidden:true },
        { field:'Jornada',	    hidden:true },
		{ field:'Background',	hidden:true },
		{ field:'LiveStream2',	hidden:true },
		{ field:'LiveStream3',	hidden:true }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='selectring';
		param.Hidden=0;
		return true;
	},
    onSelect: function(index,row) { setupByJornada(row.Prueba,row.Jornada); }
});

function vw_accept() {
	// si prueba invalida cancelamos operacion
	var s=$('#selvw-Session').combogrid('grid').datagrid('getSelected');
	if ( s===null ) {
		// indica error
		$.messager.alert("Error","Debe indicar una sesion v&aacute;lidas","error");
		return;
	}
	// clear selection to make sure next time gets empty
	$('#selvw-Session').combogrid('setValue','');
	$('#selvw-Jornada').combogrid('setValue','');
	
	// store selected data into global structure
	workingData.sesion=s.ID;
	workingData.nombreSesion=s.Nombre;
	initWorkingData(s.ID);
	var page="'/agility/console/frm_notavailable.php";
	var n=parseInt($('#selvw-Vista').val());
	switch (n){
	case 0: // Ordenes de Salida
		page="/agility/videowall/vw_ordensalida.php";
		break;
	case 1: // Llamada a pista
		page="/agility/videowall/vw_llamada.php";
		break;
	case 2: // Resultados Parciales
		page="/agility/videowall/vw_parciales.php";
		break;
	case 3: // Live Stream OSD
		page="/agility/videowall/vw_livestream.php";
		break;
    case 4: // Vista Combinada
        page="/agility/videowall/vw_combinada.php";
        break;
    case 5: // resultados parciales con livestream
        page="/agility/videowall/vwls_parciales.php";
        break;
	}
	$('#selvw-dialog').dialog('close');
	$('#vw_contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#vw_contenido').load('/agility/console/frm_notavailable.php');
				else {
					var bg=workingData.datosSesion.Background;
					var ls1=workingData.datosSesion.LiveStream;
					var ls2=workingData.datosSesion.LiveStream2;
					var ls3=workingData.datosSesion.LiveStream3;
					if ( bg !== '' ) $('#vwls_video').attr('poster', bg);
					if ( ls1!== '' ) $('#vwls_videomp4').attr('src', ls1); else $('#vwls_videomp4').remove();
					if ( ls2!== '' ) $('#vwls_videoogv').attr('src', ls2); else $('#vwls_videoogv').remove();
					if ( ls3!== '' ) $('#vwls_videowebm').attr('src', ls3); else $('#vwls_videowebm').remove();
					// if LiveStream is present load and play assigned session's livestream url
					var video=$('#vwls_video')[0];
					if (!video) return;
					video.load();
					video.play();
				}
			}
		);
}

</script>
</body>
</html> 
